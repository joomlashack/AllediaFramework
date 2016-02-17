<?php
/**
 * @package    AllediaFramework
 * @subpackage
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2016 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\Framework\AutoLoader;

defined('_JEXEC') or die();

if (!class_exists('\\Alledia\\Framework\\AutoLoader')) {
    require_once __DIR__ . '/Framework/AutoLoader.php';
}

/**
 * Class AllediaPsr4AutoLoader
 *
 * @deprecated See Alledia\Framework\AutoLoader
 */
class AllediaPsr4AutoLoader extends AutoLoader
{
    /**
     * @param string      $prefix
     * @param string     $baseDir
     * @param bool $prepend
     *
     * @return void
     *
     * @deprecated
     */
    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        static::register($prefix, $baseDir, $prepend);
    }
}
