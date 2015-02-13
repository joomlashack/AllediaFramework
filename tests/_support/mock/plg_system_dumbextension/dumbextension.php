<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Alledia\Framework\Joomla\Extension\AbstractPlugin;

/**
 * DumbExtension System Plugin
 *
 */
class PlgSystemDumbExtension extends AbstractPlugin
{
    public function __construct(&$subject, $config = array())
    {
        $this->namespace = 'DumbExtension';

        parent::__construct($subject, $config);
    }

    public function executeInit() {
        $this->init();
    }
}
