<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
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
    $loader->addNamespace('Alledia\Framework', ALLEDIA_FRAMEWORK_PATH . '/Framework');
}
