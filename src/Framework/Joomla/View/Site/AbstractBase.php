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

namespace Alledia\Framework\Joomla\View\Site;

use Alledia\Framework\Joomla\AbstractView;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

class AbstractBase extends AbstractView
{
    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @inheritDoc
     */
    public function setModel($model, $default = false)
    {
        $model = parent::setModel($model, $default);

        $this->setParams();

        return $model;
    }

    /**
     * @return void
     */
    protected function setParams()
    {
        $this->params = new Registry();

        // Load component parameters first
        $this->params->merge($this->extension->params);

        if ($activeMenu = $this->app->getMenu()->getActive()) {
            // We're on a menu - add/override its parameters
            $this->params->merge($activeMenu->getParams());

            $this->params->def('page_heading', $this->params->get('page_title') ?: $activeMenu->title);
        }
    }
}
