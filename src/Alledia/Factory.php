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
     * @param  string $element The extension element
     *
     * @return object            The license instance
     */
    public static function getExtension($element)
    {
        if (empty(self::$extensionInstances[$element])) {
            $instance = new Extension($element);

            self::$extensionInstances[$element] = $instance;
        }

        return self::$extensionInstances[$element];
    }
}
