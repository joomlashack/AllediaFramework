<?php
/**
 * @package   OSCampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2021-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSCampus.
 *
 * OSCampus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSCampus is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSCampus.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\Controller;

use Alledia\Framework\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\String\Inflector;

defined('_JEXEC') or die();

trait TraitController
{
    /**
     * For consistency between Joomla 3 & 4
     *
     * @var CMSApplication
     * @since Joomla v4
     */
    protected $app = null;

    /**
     * Standard return to calling url. In order:
     *    - Looks for base64 encoded 'return' URL variable
     *    - Uses current 'Itemid' URL variable
     *    - Uses current 'option', 'view', 'layout' URL variables
     *    - Goes to site default page
     *
     * @param ?array|string $message The message to queue up
     * @param ?string       $type    message|notice|error
     * @param ?string       $return  (optional) base64 encoded url for redirect
     *
     * @return void
     */
    protected function callerReturn($message = null, ?string $type = null, ?string $return = null)
    {
        $url = $return ?: $this->app->input->getBase64('return');
        if ($url) {
            $url = base64_decode($url);

        } else {
            $url = new Uri('index.php');

            if ($itemId = $this->app->input->getInt('Itemid')) {
                $url->setVar('Itemid', $itemId);

            } elseif ($option = $this->app->input->getCmd('option')) {
                $url->setVar('option', $option);
            }

            if ($view = $this->app->input->getCmd('view')) {
                $url->setVar('view', $view);
                if ($layout = $this->app->input->getCmd('layout')) {
                    $url->setVar('layout', $layout);
                }
            }
        }

        if (is_array($message)) {
            $message = join('<br>', $message);
        }

        $this->setRedirect(Route::_((string)$url), $message, $type);
    }

    /**
     * Return to referrer if internal, home page if external
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    protected function errorReturn(string $message, string $type = 'error')
    {
        $referrer = $this->input->server->getString('HTTP_REFERER');

        if (Uri::isInternal($referrer) == false) {
            $referrer = 'index.php';
        }

        $this->app->enqueueMessage($message, $type);

        $this->app->redirect($referrer);
    }

    /**
     * Provide consistency between Joomla 3/Joomla 4 among other possibilities
     *
     * @return void
     */
    protected function customInit()
    {
        if (empty($this->app)) {
            $this->app = Factory::getApplication();
        }
    }

    /**
     * @return Inflector
     */
    protected function getStringInflector(): Inflector
    {
        return Inflector::getInstance();
    }
}
