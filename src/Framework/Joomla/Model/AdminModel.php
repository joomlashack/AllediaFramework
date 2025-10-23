<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2025 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Model;

use Joomla\CMS\MVC;
use Joomla\CMS\Object\CMSObject;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

abstract class AdminModel extends MVC\Model\AdminModel
{
    /**
     * @inheritDoc
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        if ($item instanceof CMSObject) {
            $item = (new Registry($item->getProperties()))->toObject();
        }

        return $item;
    }
}
