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

namespace Alledia\Framework\Joomla;

use Alledia\Framework\Extension;
use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

trait TraitAllediaView
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var Extension
     */
    protected $extension = null;

    /**
     * @var bool
     */
    protected $initSuccess = null;

    /**
     * To be called before class constructor method by inheriting classes
     *
     * @return void
     */
    protected function setup()
    {
        try {
            $this->app      = Factory::getApplication();
            $this->document = Factory::getDocument();

            $this->option = $this->app->input->get('option');

            $info = ExtensionHelper::getExtensionInfoFromElement($this->option);

            $this->extension = Factory::getExtension($info['namespace'], $info['type']);
            $this->extension->loadLibrary();

            $this->initSuccess = true;

        } catch (\Throwable $error) {
            if ($this->app) {
                $this->app->enqueueMessage($error->getMessage(), 'error');
            } else {
                echo '<p>' . $error->getMessage() . '</p>';
            }

            $this->initSuccess = false;
        }
    }

    /**
     * Look for a valid layout file based on Joomla version
     *
     * @param string $layout
     *
     * @return string
     * @throws \Exception
     */
    protected function getVersionedLayoutName(string $layout): string
    {
        if (is_callable([$this, '_createFileName'])) {
            $file = $layout;
            if (Version::MAJOR_VERSION < 4) {
                $file .= '.j' . Version::MAJOR_VERSION;
            }

            if ($file != $layout || $file == 'emptystate') {
                // Verify layout file exists
                $fileName = $this->_createFileName('template', ['name' => $file]);
                $path     = Path::find($this->_path['template'], $fileName);

                if ($path) {
                    $layout = $file;
                } else {
                    $layout = ($file == 'emptystate') ? 'default' : $layout;
                }
            }

            return $layout;
        }

        throw new \Exception('TraitAllediaView must apply to a Joomla view', 500);
    }
}
