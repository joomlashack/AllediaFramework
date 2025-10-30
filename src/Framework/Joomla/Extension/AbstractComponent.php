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

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\AbstractTable;
use Alledia\Framework\Joomla\Controller\AbstractBase;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

abstract class AbstractComponent extends Licensed
{
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @var ?AbstractBase
     */
    protected $controller = null;

    /**
     * @inheritDoc
     */
    public function __construct($namespace)
    {
        parent::__construct($namespace, 'component');

        BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
        Table::addIncludePath(JPATH_COMPONENT . '/tables');

        $this->loadLibrary();
    }

    /**
     * @param string $namespace
     *
     * @return self
     */
    public static function getInstance($namespace = null)
    {
        if (static::$instance === null) {
            static::$instance = new static($namespace);
        }

        return static::$instance;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function init()
    {
        $this->loadController();
        $this->executeRedirectTask();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function loadController()
    {
        if ($this->controller === null) {
            $app    = Factory::getApplication();
            $client = $app->isClient('administrator') ? 'Admin' : 'Site';

            require JPATH_COMPONENT . '/controller.php';

            $callable = [
                '\\Alledia\\' . $this->namespace . '\\' . ucfirst($this->license) . '\\Joomla\\Controller\\' . $client,
                'getInstance',
            ];

            $this->controller = is_callable($callable)
                ? call_user_func($callable, $this->namespace)
                : null;
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function executeRedirectTask()
    {
        $app = Factory::getApplication();

        if ($this->controller) {
            $task = $app->input->getCmd('task');

            $this->controller->execute($task);
            $this->controller->redirect();

        } else {
            $referer = $app->input->getCmd('referer');
            $app->redirect($referer);
        }
    }

    /**
     * @param string $type
     *
     * @return ?BaseDatabaseModel
     */
    public function getModel(string $type): ?BaseDatabaseModel
    {
        $class = sprintf(
            'Alledia\\%s\\%s\\Joomla\\Model\\%s',
            $this->namespace,
            $this->isPro() ? 'Pro' : 'Free',
            $type
        );
        if (class_exists($class)) {
            return new $class();
        }

        return BaseDatabaseModel::getInstance($type, $this->namespace . 'Model');
    }

    /**
     * @param string $type
     *
     * @return ?Table
     */
    public function getTable(string $type): ?Table
    {
        $class = sprintf(
            'Alledia\\%s\\%s\\Joomla\\Table\\%s',
            $this->namespace,
            $this->isPro() ? 'Pro' : 'Free',
            $type
        );
        if (class_exists($class)) {
            $db = Factory::getDatabase();

            return new $class($db);
        }

        return AbstractTable::getInstance($type, $this->namespace . 'Table');
    }
}
