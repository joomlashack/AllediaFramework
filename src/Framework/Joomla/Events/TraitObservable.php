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

use Joomla\Event\Dispatcher;
use Alledia\Framework\Factory;

defined('_JEXEC') or die();

trait TraitObservable
{
    /**
     * @var JEventDispatcher|Dispatcher
     */
    protected static $coreDispatcher = null;

    /**
     * @var bool
     */
    protected static $legacyDispatch = null;

    /**
     * @return JEventDispatcher|Dispatcher
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
     * @param ?string|string[] $events
     * @param ?object          $observable
     *
     * @return void
     */
    public function registerEvents($events = null, object $observable = null)
    {
        $observable = $observable ?: $this;

        if (empty($events)) {
            // We'll take a best guess as to registrable event handlers
            $observableInfo = new \ReflectionClass($observable);
            $methods        = $observableInfo->getMethods(\ReflectionMethod::IS_PUBLIC);
            $events = [];
            foreach ($methods as $method) {
                if (strpos($method->name, 'oscampus') === 0) {
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
