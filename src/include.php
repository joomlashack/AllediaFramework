<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2018 Open Source Training, LLC., All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

    AutoLoader::register('\\Alledia\\Framework', ALLEDIA_FRAMEWORK_PATH . '/Framework');
    HTMLHelper::addIncludePath(ALLEDIA_FRAMEWORK_PATH . '/helpers/html');

    class_alias('\\Alledia\\Framework\\Joomla\Extension\Licensed', '\\Alledia\\Framework\\Extension');

    if (Version::MAJOR_VERSION < 4) {
        // Add some shims for Joomla 3
        class_alias('JHtmlSidebar', '\\Joomla\\CMS\\HTML\\Helpers\\Sidebar');
    }
}

// Backward compatibility with the old autoloader. Avoids breaking legacy extensions.
if (!class_exists('AllediaPsr4AutoLoader')) {
    require_once 'AllediaPsr4AutoLoader.php';
}

return defined('ALLEDIA_FRAMEWORK_LOADED');
