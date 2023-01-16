<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2021-2023 Joomlashack.com. All rights reserved
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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

if (Version::MAJOR_VERSION < 4) {
    class_alias('JFormFieldList', '\\Joomla\\CMS\\Form\\Field\\ListField');
    FormHelper::loadFieldClass('List');
}

class ListField extends \Joomla\CMS\Form\Field\ListField
{
    use TraitLayouts;

    /**
     * Set list field layout based on Joomla version
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $this->setListLayout();

        return parent::setup($element, $value, $group);
    }
}
