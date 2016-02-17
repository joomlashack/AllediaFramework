<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\Framework\Joomla\Extension;

defined('_JEXEC') or die();

use Alledia\Framework\Factory;
use JRegistry;
use JModuleHelper;
use JError;

abstract class AbstractFlexibleModule extends Licensed
{
    public $id;

    public $title;

    public $module;

    public $position;

    public $content;

    public $showtitle;

    public $params;

    public $menuid;

    public $name;

    public $style;


    /**
     * Class constructor that instantiate the free and pro library, if installed
     *
     * @param string $namespace  Namespace
     * @param object $module     The base module, instance of stdClass
     */
    public function __construct($namespace, $module = null)
    {
        parent::__construct($namespace, 'module');

        $this->loadLibrary();

        if (is_object($module)) {
            $this->id        = $module->id;
            $this->title     = $module->title;
            $this->module    = $module->module;
            $this->position  = $module->position;
            $this->content   = $module->content;
            $this->showtitle = $module->showtitle;
            $this->menuid    = $module->menuid;
            $this->name      = $module->name;
            $this->style     = $module->style;
            $this->params    = new JRegistry($module->params);
        }
    }

    /**
     * Method to initialize the module
     */
    public function init()
    {
        require JModuleHelper::getLayoutPath('mod_' . $this->element, $this->params->get('layout', 'default'));
    }
}
