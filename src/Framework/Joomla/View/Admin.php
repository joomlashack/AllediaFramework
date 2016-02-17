<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\Framework\Joomla\View;

defined('_JEXEC') or die();

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;
use JFile;

class Admin extends Base
{
    protected $option;

    protected $extension;

    public function __construct($config = array())
    {
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $this->option = JRequest::getCmd('option');
        } else {
            $app    = Factory::getApplication();
            $this->option = $app->input->get('option');
        }

        $info = ExtensionHelper::getExtensionInfoFromElement($this->option);
        $this->extension = Factory::getExtension($info['namespace'], $info['type']);
    }

    public function display($tpl = null)
    {
        // Add default admin CSS
        $cssPath = JPATH_SITE . "/media/{$this->option}/css/admin-default.css";
        if (file_exists($cssPath)) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet($cssPath);
        }

        parent::display($tpl);

        $this->displayFooter();
    }

    protected function displayFooter()
    {
        $output = '';

        $layoutPath = $this->extension->getExtensionPath() . '/views/footer/tmpl/default.php';
        if (JFile::exists($layoutPath)) {
            // Start capturing output into a buffer
            ob_start();

            // Include the requested template filename in the local scope
            // (this will execute the view logic).
            include $layoutPath;

            // Done with the requested template; get the buffer and
            // clear it.
            $output = ob_get_contents();
            ob_end_clean();
        }

        echo $output;
    }
}
