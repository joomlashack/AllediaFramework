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

use Joomla\CMS\HTML\HTMLHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class JhtmlAlledia
{
    /**
     * Joomla version agnostic modal select field rendering
     *
     * @param array $options
     *
     * @return string
     * @deprecated v3.3.5: Use AllediaModal::renderSelectField()
     */
    public static function renderModal(array $options): string
    {
        return HTMLHelper::_('alledia.modal.renderSelectField', $options);
    }

    /**
     * @param string $title
     * @param string $text
     * @param string $body
     *
     * @return string
     */
    public static function tooltip(string $title, string $text, string $body): string
    {
        HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

        return sprintf(
            '<span class="inactive tip-top hasTooltip" title="%s">%s</span>',
            HTMLHelper::_('tooltipText', $title . ' :: ' . $text),
            $body
        );
    }

    /**
     * Ensure Fontawesome is loaded
     *
     * @return void
     */
    public static function fontawesome(): void
    {
        HTMLHelper::_(
            'stylesheet',
            'lib_allediaframework/fontawesome/css/all.min.css',
            ['relative' => true]
        );
    }

    /**
     * Truncate a string on word boundary within limit
     * Recognize '-' and '_' along with standard whitespace
     *
     * @param string  $string
     * @param int     $limit
     * @param ?string $continued
     *
     * @return string
     */
    public static function truncate(string $string, int $limit, ?string $continued = '...'): string
    {
        if (
            mb_strlen($string) > $limit
            && mb_ereg('(.*?)[^\s_-]*\w$', trim(mb_substr($string, 0, $limit - strlen($continued) + 1) . 'a'), $match)
        ) {
            return trim(trim($match[1]), '-_') . $continued;
        }

        return $string;
    }
}
