<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */


/**
 * DumbExtensionController component Controller
 */
class DumbExtensionController extends JControllerLegacy
{
    public $executedTask = '';

    public $redirected = false;

    public function execute($task)
    {
        $this->executedTask = $task;
    }

    public function redirect()
    {
        $this->redirected = true;
    }
}
