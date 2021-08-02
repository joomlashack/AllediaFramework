<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework;

use Alledia\Framework\Joomla\Extension\Licensed;

defined('_JEXEC') or die();

abstract class Factory extends \JFactory
{
    /**
     * Instances of extensions
     *
     * @var array
     */
    protected static $extensionInstances = [];

    /**
     * Get an extension
     *
     * @param  string $namespace The extension namespace
     * @param  string $type      The extension type
     * @param  string $folder    The extension folder (plugins only)
     *
     * @return Licensed          The extension instance
     */
    public static function getExtension($namespace, $type, $folder = null)
    {
        $key = $namespace . $type . $folder;

        if (empty(static::$extensionInstances[$key])) {
            $instance = new Joomla\Extension\Licensed($namespace, $type, $folder);

            static::$extensionInstances[$key] = $instance;
        }

        return static::$extensionInstances[$key];
    }
}
