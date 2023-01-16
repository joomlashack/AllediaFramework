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

defined('_JEXEC') or die();

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\Table\Base as BaseTable;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

abstract class AbstractComponent extends Licensed
{
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @var object
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
        if (empty(static::$instance)) {
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
        if (!is_object($this->controller)) {
            $app    = Factory::getApplication();
            $client = $app->isClient('administrator') ? 'Admin' : 'Site';

            $controllerClass = 'Alledia\\' . $this->namespace . '\\' . ucfirst($this->license)
                . '\\Joomla\\Controller\\' . $client;
            require JPATH_COMPONENT . '/controller.php';

            $this->controller = $controllerClass::getInstance($this->namespace);
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function executeRedirectTask()
    {
        $app  = Factory::getApplication();
        $task = $app->input->getCmd('task');

        $this->controller->execute($task);
        $this->controller->redirect();
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
            $db = Factory::getDbo();

            return new $class($db);
        }

        return BaseTable::getInstance($type, $this->namespace . 'Table');
    }
}
