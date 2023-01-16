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

namespace Alledia\Framework\Joomla;

use Alledia\Framework\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

abstract class AbstractTable extends Table
{
    /**
     * Joomla version agnostic loading of other component tables
     *
     * @param string $component
     * @param string $name
     * @param string $prefix
     *
     * @return ?Table
     */
    public static function getComponentInstance($component, $name, $prefix): ?Table
    {
        if (Version::MAJOR_VERSION < 4) {
            Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $component . '/tables');
            $table = Table::getInstance($name, $prefix);

        } else {
            try {
                $table = Factory::getApplication()
                    ->bootComponent($component)
                    ->getMVCFactory()
                    ->createTable($name, 'Administrator');

            } catch (\Throwable $error) {
                // Ignore
            }
        }

        return $table ?? null;
    }
}
