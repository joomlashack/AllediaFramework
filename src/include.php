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

use Alledia\Framework\AutoLoader;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    define('ALLEDIA_FRAMEWORK_LOADED', 1);

    define('ALLEDIA_FRAMEWORK_PATH', __DIR__);

    if (!class_exists('\\Alledia\\Framework\\AutoLoader')) {
        require_once ALLEDIA_FRAMEWORK_PATH . '/Framework/AutoLoader.php';
    }
    include ALLEDIA_FRAMEWORK_PATH . '/vendor/autoload.php';

    AutoLoader::register('\\Alledia\\Framework', ALLEDIA_FRAMEWORK_PATH . '/Framework');
    HTMLHelper::addIncludePath(ALLEDIA_FRAMEWORK_PATH . '/helpers/html');

    class_alias('\\Alledia\\Framework\\Joomla\Extension\Licensed', '\\Alledia\\Framework\\Extension');

    if (Version::MAJOR_VERSION < 4) {
        // Add some shims for Joomla 3
        class_alias('JHtmlSidebar', '\\Joomla\\CMS\\HTML\\Helpers\\Sidebar');
    }
}

return defined('ALLEDIA_FRAMEWORK_LOADED');
