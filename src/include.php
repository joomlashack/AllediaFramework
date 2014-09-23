<?php
/**
 * @package   AllediaFramework
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use \Alledia;

defined('_JEXEC') or die();

if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    define('ALLEDIA_FRAMEWORK_LOADED', 1);

    define('ALLEDIA_FRAMEWORK_PATH', __DIR__);

    // Setup autoloaded libraries
    require_once ALLEDIA_FRAMEWORK_PATH . '/Psr4AutoLoader.php';
    $loader = new Psr4AutoLoader();
    $loader->register();
    $loader->addNamespace('Alledia', ALLEDIA_FRAMEWORK_PATH . '/Alledia');
}
