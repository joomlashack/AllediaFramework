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

defined('_JEXEC') or die();

/**
 * Generic extension helper class
 */
abstract class Helper
{
    /**
     * @var array[]
     */
    protected static $extensionInfo = [];

    protected static $extensionTypes = [
        'com' => 'component',
        'plg' => 'plugin',
        'mod' => 'module',
        'lib' => 'library',
        'tpl' => 'template',
        'cli' => 'cli'
    ];

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
        $prefix = array_search($type, static::$extensionTypes);
        if ($prefix) {
            if (strpos($element, $prefix) === 0) {
                $shortElement = substr($element, strlen($prefix) + 1);
            }
            $parts = [
                $prefix,
                $type == 'plugin' ? $folder : null,
                $shortElement ?? $element
            ];

            return join('_', array_filter($parts));
        }

        return null;
    }

    /**
     * @param ?string $element
     *
     * @return ?array
     */
    public static function getExtensionInfoFromElement(?string $element): ?array
    {
        if (isset(static::$extensionInfo[$element]) == false) {
            static::$extensionInfo[$element] = false;

            $parts = explode('_', $element, 3);
            if (count($parts) > 1) {
                $prefix = $parts[0];
                $name   = $parts[2] ?? $parts[1];
                $group  = empty($parts[2]) ? null : $parts[1];

                $types = [
                    'com' => 'component',
                    'plg' => 'plugin',
                    'mod' => 'module',
                    'lib' => 'library',
                    'tpl' => 'template',
                    'cli' => 'cli'
                ];

                if (array_key_exists($prefix, $types)) {
                    $result = [
                        'prefix'    => $prefix,
                        'type'      => $types[$prefix],
                        'name'      => $name,
                        'group'     => $group,
                        'namespace' => preg_replace_callback(
                            '/^(os[a-z])(.*)/i',
                            function ($matches) {
                                return strtoupper($matches[1]) . $matches[2];
                            },
                            $name
                        )
                    ];
                }

                static::$extensionInfo[$element] = $result;
            }
        }

        return static::$extensionInfo[$element] ?: null;
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
