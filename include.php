<?php
/**
 * @package   Alledia
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use \Alledia;

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');

if (!defined('ALLEDIA_LOADED')) {
    define('ALLEDIA_LOADED', 1);
    define('ALLEDIA_VERSION', '1.0.0');

    define('ALLEDIA_PATH_LIBRARY', __DIR__);

    // Setup autoloaded libraries
    require_once ALLEDIA_PATH_LIBRARY . '/Psr4AutoLoader.php';
    $loader = new Psr4AutoLoader();
    $loader->register();
    $loader->addNamespace('Alledia', ALLEDIA_PATH_LIBRARY . '/alledia');
}


// Fixed here
