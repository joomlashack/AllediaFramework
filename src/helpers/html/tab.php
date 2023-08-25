<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2023 Joomlashack.com. All rights reserved
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class AllediaTab
{
    /**
     * @param string $selector
     * @param ?array $params
     *
     * @return string
     */
    public static function startTabSet(string $selector = 'myTab', array $params = []): string
    {
        if (Version::MAJOR_VERSION < 4) {
            return HTMLHelper::_('bootstrap.startTabSet', $selector, $params);
        }

        return HTMLHelper::_('uitab.startTabSet', $selector, $params);
    }

    /**
     * @param string $selector
     * @param string $id
     * @param string $title
     *
     * @return string
     */
    public static function addTab(string $selector, string $id, string $title): string
    {
        if (Version::MAJOR_VERSION < 4) {
            return HTMLHelper::_('bootstrap.addTab', $selector, $id, $title);
        }

        return HTMLHelper::_('uitab.addTab', $selector, $id, $title);
    }

    /**
     * @return string
     */
    public static function endTab(): string
    {
        if (Version::MAJOR_VERSION < 4) {
            return HTMLHelper::_('bootstrap.endTab');
        }

        return HTMLHelper::_('uitab.endTab');
    }

    /**
     * @return string
     */
    public static function endTabset(): string
    {
        if (Version::MAJOR_VERSION < 4) {
            return HTMLHelper::_('bootstrap.endTabSet');
        }

        return HTMLHelper::_('uitab.endTabSet');
    }
}
