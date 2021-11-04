<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2021 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Form\Field;

use Joomla\CMS\Version;

defined('_JEXEC') or die();

class ListField extends \Joomla\CMS\Form\Field\ListField
{
    /**
     * Set list field layout based on Joomla version
     * Must be called AFTER setup() method of list form field
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (Version::MAJOR_VERSION >= 4 && empty($element['layout'])) {
            $this->layout = 'joomla.form.field.list-fancy-select';
        }

        return parent::setup($element, $value, $group);
    }
}
