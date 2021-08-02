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

defined('_JEXEC') or die();

use JControllerLegacy;
use Joomla\CMS\Factory;

class Component extends Licensed
{
    /**
     * The main controller
     *
     * @var JControllerLegacy
     */
    protected $controller;

    /**
     * Class constructor that instantiate the free and pro library, if installed
     */
    public function __construct($namespace)
    {
        parent::__construct($namespace, 'component');

        $this->loadLibrary();
    }

    /**
     * Load the main controller
     *
     * @return void
     * @throws \Exception
     */
    public function loadController()
    {
        if (!isset($this->controller)) {
            jimport('legacy.controller.legacy');

            $this->controller = JControllerLegacy::getInstance($this->namespace);
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function executeTask()
    {
        $task = Factory::getApplication()->input->getCmd('task');

        $this->controller->execute($task);
        $this->controller->redirect();
    }
}
