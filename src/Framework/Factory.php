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

namespace Alledia\Framework;

use Alledia\Framework\Joomla\Extension\Licensed;
use JEventDispatcher;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;

defined('_JEXEC') or die();

abstract class Factory extends \Joomla\CMS\Factory
{
    /**
     * Instances of extensions
     *
     * @var array
     */
    protected static $extensionInstances = [];

    /**
     * @var JEventDispatcher|DispatcherInterface
     */
    protected static $dispatcher = null;

    /**
     * Get an extension
     *
     * @param string $namespace The extension namespace
     * @param string $type      The extension type
     * @param string $folder    The extension folder (plugins only)
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

    /**
     * @return \JDatabaseDriver|DatabaseDriver
     */
    public static function getDatabase()
    {
        if (is_callable([static::class, 'getContainer'])) {
            return static::getContainer()->get('DatabaseDriver');
        }

        return static::getDbo();
    }

    /**
     * @return JEventDispatcher|DispatcherInterface
     */
    public static function getDispatcher()
    {
        if (Version::MAJOR_VERSION < 4) {
            if (static::$dispatcher === null) {
                static::$dispatcher = JEventDispatcher::getInstance();
            }

            return static::$dispatcher;
        }

        return static::getApplication()->getDispatcher();
    }

    /**
     * @param string $eventName
     * @param array  $args
     *
     * @return array
     */
    public static function triggerEvent(string $eventName, array $args)
    {
        try {
            $dispatcher = static::getDispatcher();
        } catch (\UnexpectedValueException $exception) {
            // ignore for now
            return [];
        }

        if (Version::MAJOR_VERSION < 4) {
            $result = $dispatcher->trigger($eventName, $args);

        } else {
            $event  = new Event($eventName, $args);
            $result = $dispatcher->dispatch($eventName, $event);
            $result = $result['result'] ?? [];
        }

        return $result;
    }
}
