<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2021-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\View\Admin;

use Alledia\Framework\Extension;
use Alledia\Framework\Joomla\AbstractView;
use Joomla\CMS\Filesystem\File;

defined('_JEXEC') or die();

class AbstractBase extends AbstractView
{
    /**
     * @inheritDoc
     */
    protected function displayFooter(?Extension $extension = null)
    {
        parent::displayFooter();

        echo $this->displayAdminFooter($extension);
    }

    /**
     * @param ?Extension $extension
     *
     * @return string
     */
    protected function displayAdminFooter(?Extension $extension = null): string
    {
        $extension = $extension ?: ($this->extension ?? null);

        if ($extension) {
            $output = $extension->getFooterMarkup();

            if (empty($output)) {
                // Use alternative if no custom footer field
                $layoutPath = $extension->getExtensionPath() . '/views/footer/tmpl/default.php';

                if (!File::exists($layoutPath)) {
                    $layoutPath = $extension->getExtensionPath() . '/alledia_views/footer/tmpl/default.php';
                }

                if (File::exists($layoutPath)) {
                    ob_start();
                    include $layoutPath;
                    $output = ob_get_contents();
                    ob_end_clean();
                }
            }
        }

        return $output ?? '';
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function setLayout($layout)
    {
        $layout = $this->getVersionedLayoutName($layout);

        return parent::setLayout($layout);
    }
}
