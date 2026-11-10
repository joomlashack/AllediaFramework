<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2026 Joomlashack.com. All rights reserved
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

// phpcs:disable PSR1.Files.SideEffects
use Joomla\CMS\Form\Field\NumberField as JoomlaNumberField;

defined('_JEXEC') or die();

if (class_exists(JoomlaNumberField::class) == false) {
    // Not FormHelper::loadFieldClass(): it searches extension field paths too, and could load
    // an extension's own number.php that extends this class before the class exists
    require_once JPATH_LIBRARIES . '/joomla/form/fields/number.php';
    class_alias(\JFormFieldNumber::class, JoomlaNumberField::class);
}

// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class NumberField extends JoomlaNumberField
{
    use TraitLayouts;
}
