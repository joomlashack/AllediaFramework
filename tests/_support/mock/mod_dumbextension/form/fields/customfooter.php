<?php
/**
 * @package   AllediaFreeDefaultFiles
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Form field to show an advertisement for the pro version
 */
class JFormFieldCustomFooter extends JFormField
{
    public $fromInstaller = false;

    protected $class = '';

    protected $media;

    protected $attributes;

    protected $element;

    public function __construct()
    {
        $this->element = new stdClass;
    }

    protected function getInput()
    {
        return '##alledia-footer-markup##';
    }

    public function getInputUsingCustomElement($field)
    {
        return $this->getInput();
    }
}
