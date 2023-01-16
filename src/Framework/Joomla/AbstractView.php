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

namespace Alledia\Framework\Joomla;

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;

defined('_JEXEC') or die();

abstract class AbstractView extends HtmlView
{
    use TraitAllediaView;

    /**
     * @var BaseDatabaseModel
     */
    protected $model = null;

    /**
     * Formally declare this since Joomla core does not
     *
     * @var Form
     */
    protected $form = null;

    /**
     * @var CMSObject
     */
    protected $state = null;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        $this->setup();

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function setModel($model, $default = false)
    {
        $model = parent::setModel($model, $default);
        if ($model && $default) {
            $this->model = $model;
            $this->state = $this->model->getState();
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function display($tpl = null)
    {
        if ($this->initSuccess) {
            $this->displayHeader();

            parent::display($tpl);

            $this->displayFooter();
        }
    }

    /**
     * For use by subclasses
     *
     * @return void
     */
    protected function displayHeader()
    {
        // Display custom text
    }

    /**
     * For use by subclasses
     *
     * @return void
     */
    protected function displayFooter()
    {
        // Display custom text
    }

    /**
     * @param string $name
     * @param string $layout
     *
     * @return string
     * @throws \Exception
     */
    public function loadDefaultTemplate(string $name, ?string $layout = 'default'): string
    {
        $currentLayout = $this->setLayout($layout);

        $output = $this->loadTemplate($name);

        $this->setLayout($currentLayout);

        return $output;
    }
}
