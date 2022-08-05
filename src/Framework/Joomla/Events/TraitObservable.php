<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2022 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Events;

use Alledia\Framework\Factory;
use Joomla\Event\Dispatcher;

defined('_JEXEC') or die();

trait TraitObservable
{
    /**
     * @var \JEventDispatcher|Dispatcher
     */
    protected static $coreDispatcher = null;

    /**
     * @var bool
     */
    protected static $legacyDispatch = null;

    /**
     * @return \JEventDispatcher|Dispatcher
     */
    protected function getDispatcher()
    {
        if (static::$coreDispatcher === null) {
            static::$coreDispatcher = Factory::getDispatcher();
            static::$legacyDispatch = is_callable([static::$coreDispatcher, 'register']);
        }

        return static::$coreDispatcher;
    }

    /**
     * $events is accepted in these forms:
     *
     * ['handler1', 'handler2',...]: array of specific methods to register
     * 'handler'                   : a single method to register
     * 'prefix*'                   : all public methods in $observable that begin with 'prefix'
     * '*string'                   : all public methods in $observable that contain 'string'
     *
     * @param string|string[] $events
     * @param ?object         $observable
     *
     * @return void
     */
    public function registerEvents($events, object $observable = null)
    {
        $observable = $observable ?: $this;

        if (is_string($events) && strpos($events, '*') !== false) {
            $startsWith = strpos($events, '*') !== 0;
            $event      = preg_replace('/[^a-z\d_]/i', '', $events);

            // Look for methods that match the wildcarded name
            $observableInfo = new \ReflectionClass($observable);
            $methods        = $observableInfo->getMethods(\ReflectionMethod::IS_PUBLIC);

            $events = [];
            foreach ($methods as $method) {
                $position = strpos($method->name, $event);
                if (
                    ($startsWith && $position === 0)
                    || ($startsWith == false && $position > 0)
                ) {
                    $events[] = $method->name;
                }
            }

        } elseif (is_string($events)) {
            $events = [$events];
        }

        if ($events && is_array($events)) {
            $dispatcher = Factory::getDispatcher();
            foreach ($events as $event) {
                $handler = [$observable, $event];
                if (is_callable([$dispatcher, 'register'])) {
                    $dispatcher->register($event, $handler);
                } elseif (is_callable([$dispatcher, 'addListener'])) {
                    $dispatcher->addListener($event, $handler);
                }
            }
        }
    }
}
