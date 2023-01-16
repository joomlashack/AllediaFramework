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
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

/**
 * @deprecated  1.4.1 Use AbstractFlexibleModule instead. This module doesn't
 * work with multiple modules in the same page because of the Singleton pattern.
 *
 */
abstract class AbstractModule extends Licensed
{
    /**
     * @var AbstractModule
     */
    protected static $instance;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $module = null;

    /**
     * @var string
     */
    public $position = null;

    /**
     * @var string
     */
    public $content = null;

    /**
     * @var bool
     */
    public $showtitle = null;

    /**
     * @var int
     */
    public $menuid = null;

    /**
     * @var string
     */
    public $style = null;


    /**
     * @inheritDoc
     */
    public function __construct($namespace)
    {
        parent::__construct($namespace, 'module');

        $this->loadLibrary();
    }

    /**
     * Returns the instance of child classes
     *
     * @param string $namespace
     * @param object $module
     *
     * @return Object
     */
    public static function getInstance($namespace = null, $module = null)
    {
        if (empty(static::$instance)) {
            $instance = new static($namespace);

            if (is_object($module)) {
                $instance->id        = $module->id;
                $instance->title     = $module->title;
                $instance->module    = $module->module;
                $instance->position  = $module->position;
                $instance->content   = $module->content;
                $instance->showtitle = $module->showtitle;
                $instance->menuid    = $module->menuid;
                $instance->name      = $module->name;
                $instance->style     = $module->style;
                $instance->params    = new Registry($module->params);
            } else {
                // @TODO: Raise warning/Error
            }

            $instance->loadLanguage();

            static::$instance = $instance;

        }


        return static::$instance;
    }

    /**
     * @return void
     */
    public function init()
    {
        require ModuleHelper::getLayoutPath('mod_' . $this->element, $this->params->get('layout', 'default'));
    }

    /**
     * @return void
     */
    public function loadLanguage()
    {
        $language = Factory::getLanguage();
        $language->load($this->module, JPATH_SITE);
    }
}
