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

namespace Alledia\Framework\Joomla\View\Admin;

defined('_JEXEC') or die();

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Version;

abstract class AbstractList extends AbstractBase
{
    /**
     * @var object[]
     */
    protected $items = null;

    /**
     * @var string
     */
    protected $sidebar = null;

    /**
     * @var Pagination
     */
    protected $pagination = null;

    /**
     * @var Form
     */
    public $filterForm = null;

    /**
     * @var string[]
     */
    public $activeFilters = null;

    /**
     * @var bool
     */
    protected $isEmptyState = null;

    public function display($tpl = null)
    {
        // Add default admin CSS
        HTMLHelper::_('stylesheet', $this->option . '/admin-default.css', ['relative' => true]);

        if (
            Version::MAJOR_VERSION > 3
            && empty($this->items)
            && ($this->isEmptyState = $this->get('IsEmptyState'))
        ) {
            $this->setLayout('emptystate');
        }

        parent::display($tpl);
    }
}

