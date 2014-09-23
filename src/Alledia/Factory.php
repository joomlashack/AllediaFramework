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
     * Instances of licenses, by extension
     *
     * @var array
     */
    protected static $licenseInstances;

    /**
     * Get the license for a specific extension
     *
     * @param  string $element The extension element
     *
     * @return object            The license instance
     */
    public static function getLicense($element)
    {
        if (empty(self::$licenseInstances[$element])) {
            $instance = new License($element);

            self::$licenseInstances[$element] = $instance;
        }

        return self::$licenseInstances[$element];
    }
}
