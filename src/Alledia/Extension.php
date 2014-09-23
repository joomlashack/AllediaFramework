<?php
/**
 * @package   AllediaLibrary
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013-2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia;

defined('_JEXEC') or die();

class Extension extends \JObject
{
    /**
     * The extension type
     *
     * @var string
     */
    protected $type;

    /**
     * The extension name
     *
     * @var string
     */
    protected $name;

    /**
     * The extension params
     *
     * @var JRegistry
     */
    protected $params;

    /**
     * The element of the extension
     *
     * @var string
     */
    protected $element;

    /**
     * License type: free or pro
     *
     * @var string
     */
    protected $license;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * The include path for the pro library
     *
     * @var string
     */
    protected $proIncludePath;

    /**
     * Class constructor, set the extension type.
     *
     * @param string $type    The type of extension
     * @param string $element The element of the extension
     * @param string $folder  The folder for plugins (only)
     */
    public function __construct($type, $element, $folder = '', $basePath = JPATH_SITE)
    {
        $this->type = $type;
        $this->element = $element;
        $this->folder = $folder;
        $this->basePath = $basePath;

        $this->getProIncludePathForElement();

        $this->license = file_exists($this->proIncludePath) ? 'pro' : 'free';

        // Load the extension info from database
        // $db = \JFactory::getDatabase();
        // $query = $db->getQuery(true)
        //     ->select(array('type', '
        //     element', 'enabled', 'params'))
        //     ->from('#__extensions')
        //     ->where('')
    }

    /**
     * Check if the license is pro
     *
     * @return boolean True for pro license
     */
    public function isPro()
    {
        return $this->license === 'pro';
    }

    /**
     * Get the include path for the include on the pro library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getProIncludePathForElement()
    {
        if (empty($this->proIncludePath)) {
            $basePath = '';

            $folders = array(
                'component' => 'administrator/components/',
                'plugin' => 'plugins/',
                'template' => 'templates/',
                'library' => 'libraries/',
                'cli' => 'cli/',
                'module' => 'modules/'
            );

            $basePath = $this->basePath . '/' . $folders[$this->type];

            if ($this->type === 'plugin') {
                $basePath .= $this->folder . '/' . $this->element;
            } elseif ($this->type === 'module') {
                $basePath .= 'mod_' . $this->element;
            } else {
                $basePath .= $this->element;
            }

            $this->proIncludePath = $basePath . '/library/pro/include.php';
        }

        return $this->proIncludePath;
    }
}
