<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2022 Joomlashack.com. All rights reserved
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

use Alledia\Framework\AutoLoader;

defined('_JEXEC') or die();

if (!class_exists('\\Alledia\\Framework\\AutoLoader')) {
    require_once __DIR__ . '/Framework/AutoLoader.php';
}

/**
 * Class AllediaPsr4AutoLoader
 *
 * @deprecated See Alledia\Framework\AutoLoader
 */
class AllediaPsr4AutoLoader extends AutoLoader
{
    /**
     * @param string $prefix
     * @param string $baseDir
     * @param bool   $prepend
     *
     * @return void
     *
     * @deprecated
     */
    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        static::register($prefix, $baseDir, $prepend);
    }
}
