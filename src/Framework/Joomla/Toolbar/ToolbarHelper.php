<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2022-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Toolbar;

use Alledia\Framework\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

abstract class ToolbarHelper extends \Joomla\CMS\Toolbar\ToolbarHelper
{
    /**
     * @var bool
     */
    protected static $exportLoaded = false;

    /**
     * Create a button that links to an external page
     *
     * @param string  $url
     * @param string  $title
     * @param ?string $icon
     * @param ?mixed  $attributes
     *
     * @return void
     */
    public static function externalLink(string $url, string $title, ?string $icon = null, $attributes = [])
    {
        if (is_string($attributes)) {
            $attributes = Utility::parseAttributes($attributes);
        }

        $icon                 = $icon ?: 'link';
        $attributes['target'] = $attributes['target'] ?? '_blank';
        $attributes['class']  = join(
            ' ',
            array_filter(
                array_merge(
                    explode(' ', $attributes['class'] ?? ''),
                    [
                        'btn',
                        'btn-small'
                    ]
                )
            )
        );

        $button = HTMLHelper::_(
            'link',
            $url,
            sprintf('<span class="icon-%s"></span> %s', $icon, $title),
            $attributes
        );

        if (Version::MAJOR_VERSION > 3) {
            $button = sprintf('<joomla-toolbar-button>%s</joomla-toolbar-button>', $button);
        }

        $bar = Toolbar::getInstance();
        $bar->appendButton('Custom', $button, $icon);
    }

    /**
     * Create a button that links to documentation
     *
     * @param string $url
     * @param string $title
     *
     * @return void
     */
    public static function shackDocumentation(string $url, string $title)
    {
        static::externalLink($url, $title, 'support', ['class' => 'btn-info', 'target' => 'shackdocs']);
    }

    /**
     * Export button that ensures the task input field is cleared if
     * the export does not return to refresh the page
     *
     * @param string $task
     * @param string $alt
     * @param string $icon
     *
     * @return void
     * @throws \Exception
     */
    public static function exportView(string $task, string $alt, string $icon = 'download')
    {
        static::custom($task, $icon, null, $alt, false);

        if (static::$exportLoaded == false) {
            Factory::getApplication()->getDocument()->addScriptDeclaration(<<<JSCRIPT
Joomla.submitbutton = function(task) {
    Joomla.submitform(task);

    let taskInput = document.querySelector('input[name="task"]');
    if (taskInput) {
        taskInput.value = '';
    }
};
JSCRIPT
            );

            static::$exportLoaded = true;
        }
    }
}
