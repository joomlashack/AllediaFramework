<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\Framework\Joomla\Extension;

defined('_JEXEC') or die();

use Alledia\Framework\Factory;
use JRequest;
use JModelLegacy;

abstract class AbstractComponent extends Licensed
{
    private static $instance;

    /**
     * The main controller
     *
     * @var object
     */
    protected $controller;

    /**
     * Class constructor that instantiate the free and pro library, if installed
     */
    public function __construct($namespace)
    {
        parent::__construct($namespace, 'component');

        JModelLegacy::addIncludePath(JPATH_COMPONENT . '/models');

        $this->loadLibrary();
    }

    /**
     * Returns the instance of child classes
     *
     * @param string $namespace
     *
     * @return Object
     */
    public static function getInstance($namespace = null)
    {
        if (empty(static::$instance)) {
            static::$instance = new static($namespace);
        }

        return static::$instance;
    }

    public function init()
    {
        $app = Factory::getApplication();

        $this->loadController();
        $this->executeRedirectTask();
    }

    public function loadController()
    {
        if (! is_object($this->controller)) {
            $app    = Factory::getApplication();
            $client = (get_class($app) === 'JApplicationSite') ? 'Site' : 'Admin';

            $controllerClass  = 'Alledia\\' . $this->namespace . '\\' . ucfirst($this->license)
                . '\Joomla\Controller\\' . $client;
            require JPATH_COMPONENT . '/controller.php';

            $this->controller = $controllerClass::getInstance($this->namespace);
        }
    }

    public function executeRedirectTask()
    {
        // Joomla 2.5 Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $task = JRequest::getCmd('task');
        } else {
            $app = Factory::getApplication();
            $task = $app->input->getCmd('task');
        }

        $this->controller->execute($task);
        $this->controller->redirect();
    }
}
