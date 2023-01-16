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

namespace Alledia\Framework\Joomla\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;

defined('_JEXEC') or die();

class Form extends FormController
{
    use TraitController;

    /**
     * @inheritDoc
     */
    public function __construct(
        $config = [],
        MVCFactoryInterface $factory = null,
        ?CMSApplication $app = null,
        ?Input $input = null
    ) {
        parent::__construct($config, $factory, $app, $input);

        $this->customInit();
    }


    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function batch($model = null)
    {
        $this->checkToken();

        $inflector = $this->getStringInflector();
        $view      = $this->app->input->getCmd('view', $this->default_view);

        if ($inflector->isPlural($view)) {
            $modelName = $inflector->toSingular($view);

            $model = $this->getModel($modelName, '', []);

            $linkQuery = http_build_query([
                'option' => $this->app->input->getCmd('option'),
                'view'   => $view
            ]);
            $this->setRedirect(Route::_('index.php?' . $linkQuery . $this->getRedirectToListAppend(), false));

            return parent::batch($model);
        }

        return null;
    }
}
