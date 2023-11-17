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

namespace Alledia\Framework\Joomla\Extension;

use Alledia\Framework\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Version;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects

abstract class AbstractPlugin extends CMSPlugin
{
    /**
     * Alledia Extension instance
     *
     * @var Licensed
     */
    protected $extension;

    /**
     * Library namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Method used to load the extension data. It is not on the constructor
     * because this way we can avoid loading the data if the plugin
     * will not be used.
     *
     * @return void
     */
    protected function init()
    {
        $this->loadExtension();

        // Load the libraries, if existent
        $this->extension->loadLibrary();

        $this->loadLanguage();
    }

    /**
     * Method to load the language files
     *
     * @return void
     */
    public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
    {
        parent::loadLanguage($extension, $basePath);

        $systemStrings = 'plg_' . $this->_type . '_' . $this->_name . '.sys';
        parent::loadLanguage($systemStrings, $basePath);
    }

    /**
     * Method to load the extension data
     *
     * @return void
     */
    protected function loadExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = Factory::getExtension($this->namespace, 'plugin', $this->_type);
        }
    }

    /**
     * Check if this extension is licensed as pro
     *
     * @return bool True for pro version
     */
    protected function isPro()
    {
        return $this->extension->isPro();
    }
}
