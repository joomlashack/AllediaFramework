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

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

class AbstractForm extends AbstractBase
{
    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var bool
     */
    protected $useCoreUI = true;

    /**
     * @inheritDoc
     */
    protected function setup()
    {
        parent::setup();

        if (Version::MAJOR_VERSION < 4) {
            HTMLHelper::_('behavior.tabstate');
        }
    }

    /**
     * @inheritDoc
     *
     */
    public function setModel($model, $default = false)
    {
        $model = parent::setModel($model, $default);
        if ($model instanceof AdminModel && $default) {
            $this->form = $model->getForm();
        }

        return $model;
    }
}
