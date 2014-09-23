<?php
/**
 * @package   AllediaLibrary
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013-2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\Extension;

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

abstract class AbstractPlugin extends \JPlugin
{
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
    }
}
