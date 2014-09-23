<?php
/**
 * @package   AllediaLibrary
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013-2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia;

defined('_JEXEC') or die();

abstract class Factory extends \JFactory
{
    /**
     * Instances of extensions
     *
     * @var array
     */
    protected static $extensionInstances;

    /**
     * Get an extension
     *
     * @param  string $namespace The extension namespace
     * @param  string $type      The extension type
     * @param  string $folder    The extension folder (plugins only)
     *
     * @return object            The extension instance
     */
    public static function getExtension($namespace, $type, $folder = null)
    {
        $key = $namespace . $type . $folder;

        if (empty(self::$extensionInstances[$key])) {
            $instance = new Extension($namespace, $type, $folder);

            self::$extensionInstances[$key] = $instance;
        }

        return self::$extensionInstances[$key];
    }
}
