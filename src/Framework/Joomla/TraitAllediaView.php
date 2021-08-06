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

namespace Alledia\Framework\Joomla;

use Alledia\Framework\Extension;
use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Filesystem\File;

defined('_JEXEC') or die();

trait TraitAllediaView
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var Extension
     */
    protected $extension = null;

    final protected function constructSetup()
    {
        $this->app = Factory::getApplication();

        $this->option = $this->app->input->get('option');

        $info = ExtensionHelper::getExtensionInfoFromElement($this->option);

        $this->extension = Factory::getExtension($info['namespace'], $info['type']);
        $this->extension->loadLibrary();

        $this->setup();
    }

    protected function setup()
    {

    }
}