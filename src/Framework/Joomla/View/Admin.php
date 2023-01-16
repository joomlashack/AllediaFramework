<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of AllediaFramework.
 *
 * AllediaFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * AllediaFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AllediaFramework.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\View;

defined('_JEXEC') or die();

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;
use Alledia\Framework\Joomla\Extension\Licensed;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * @deprecated v2.0.5
 */
class Admin extends Base
{
    /**
     * @var string
     */
    protected $option = null;

    /**
     * @var Licensed
     */
    protected $extension = null;

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $info = ExtensionHelper::getExtensionInfoFromElement($this->option);

        $this->option    = Factory::getApplication()->input->get('option');
        $this->extension = Factory::getExtension($info['namespace'], $info['type']);
    }

    public function display($tpl = null)
    {
        // Add default admin CSS
        HTMLHelper::_('stylesheet', $this->option . '/admin-default.css', ['relative' => true]);

        parent::display($tpl);

        $this->displayFooter();
    }

    protected function displayFooter()
    {
        $output = '';

        $layoutPath = $this->extension->getExtensionPath() . '/views/footer/tmpl/default.php';
        if (!File::exists($layoutPath)) {
            $layoutPath = $this->extension->getExtensionPath() . '/alledia_views/footer/tmpl/default.php';

            if (!File::exists($layoutPath)) {
                $layoutPath = null;
            }
        }

        if (!is_null($layoutPath)) {
            // Start capturing output into a buffer
            ob_start();

            // Include the requested template filename in the local scope
            // (this will execute the view logic).
            include $layoutPath;

            // Done with the requested template; get the buffer and
            // clear it.
            $output = ob_get_contents();
            ob_end_clean();
        }

        echo $output;
    }
}
