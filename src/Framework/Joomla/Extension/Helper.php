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

namespace Alledia\Framework\Joomla\Extension;

use Alledia\Framework\Factory;

defined('_JEXEC') or die();

/**
 * Generic extension helper class
 */
abstract class Helper
{
    /**
     * Build a string representing the element
     *
     * @param string $type
     * @param string $element
     * @param string $folder
     *
     * @return string
     */
    public static function getFullElementFromInfo($type, $element, $folder = null)
    {
        $prefixes = [
            'component' => 'com',
            'plugin'    => 'plg',
            'template'  => 'tpl',
            'library'   => 'lib',
            'cli'       => 'cli',
            'module'    => 'mod'
        ];

        $fullElement = $prefixes[$type];

        if ($type === 'plugin') {
            $fullElement .= '_' . $folder;
        }

        $fullElement .= '_' . $element;

        return $fullElement;
    }

    /**
     * @param string $element
     *
     * @return array
     */
    public static function getExtensionInfoFromElement($element)
    {
        $result = [
            'type'      => null,
            'name'      => null,
            'group'     => null,
            'prefix'    => null,
            'namespace' => null
        ];

        $types = [
            'com' => 'component',
            'plg' => 'plugin',
            'mod' => 'module',
            'lib' => 'library',
            'tpl' => 'template',
            'cli' => 'cli'
        ];

        $element = explode('_', $element);

        $result['prefix'] = $element[0];

        if (array_key_exists($result['prefix'], $types)) {
            $result['type'] = $types[$result['prefix']];

            if ($result['prefix'] === 'plg') {
                $result['group'] = $element[1];
                $result['name']  = $element[2];
            } else {
                $result['name']  = $element[1];
                $result['group'] = null;
            }
        }

        $result['namespace'] = preg_replace_callback(
            '/^(os[a-z])(.*)/i',
            function ($matches) {
                return strtoupper($matches[1]) . $matches[2];
            },
            $result['name']
        );

        return $result;
    }

    /**
     * @param string $element
     *
     * @return bool
     */
    public static function loadLibrary($element)
    {
        $extension = static::getExtensionForElement($element);

        if (is_object($extension)) {
            return $extension->loadLibrary();
        }

        return false;
    }

    /**
     * @param string $element
     *
     * @return string
     */
    public static function getFooterMarkup($element)
    {
        if (is_string($element)) {
            $extension = static::getExtensionForElement($element);
        } elseif (is_object($element)) {
            $extension = $element;
        }

        if (!empty($extension)) {
            return $extension->getFooterMarkup();
        }

        return '';
    }

    /**
     * @param string $element
     *
     * @return Licensed
     */
    public static function getExtensionForElement($element)
    {
        $info = static::getExtensionInfoFromElement($element);

        if (!empty($info['type']) && !empty($info['namespace'])) {
            return Factory::getExtension($info['namespace'], $info['type'], $info['group']);
        }

        return null;
    }
}
