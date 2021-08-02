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

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

abstract class AbstractFlexibleModule extends Licensed
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var
     */
    public $module = null;

    /**
     * @var
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
    public $name = null;

    /**
     * @var string
     */
    public $style = null;

    /**
     * @var Registry
     */
    public $params = null;

    /**
     * Class constructor that instantiate the free and pro library, if installed
     *
     * @param string $namespace Namespace
     * @param object $module    The base module, instance of stdClass
     */
    public function __construct($namespace, $module = null)
    {
        parent::__construct($namespace, 'module');

        $this->loadLibrary();

        if (is_object($module)) {
            $properties = [
                'id',
                'title',
                'module',
                'position',
                'content',
                'showtitle',
                'menuid',
                'name',
                'style',
                'params'
            ];
            foreach ($properties as $property) {
                if (isset($module->{$property})) {
                    $this->{$property} = $module->{$property};
                }
            }
            if (!$this->params instanceof Registry) {
                $this->params = new Registry($this->params);
            }
        }
    }

    /**
     * Method to initialize the module
     */
    public function init()
    {
        require ModuleHelper::getLayoutPath('mod_' . $this->element, $this->params->get('layout', 'default'));
    }
}
