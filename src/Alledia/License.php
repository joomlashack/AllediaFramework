<?php
/**
 * @package   AllediaLibrary
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013-2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia;

defined('_JEXEC') or die();

class License
{
    /**
     * The element of the extension
     *
     * @var string
     */
    protected $element;

    /**
     * License type: free or pro
     */
    protected $type;

    /**
     * The include path for the pro library
     */
    protected $proIncludePath;

    /**
     * Class constructor, set the extension type.
     *
     * @param string $element The element for extension
     */
    public function __construct($element)
    {
        $this->element = $element;

        $this->getProIncludePathForElement();

        $this->type = file_exists($this->proIncludePath) ? 'pro' : 'free';
    }

    /**
     * Check if the license is pro
     *
     * @return boolean True for pro license
     */
    public function isPro()
    {
        return $this->type === 'pro';
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
            $element = $this->element;

            $extensionType = substr($element, 0, 3);

            $folders = array(
                'com' => 'administrator/components',
                'plg' => 'plugins',
                'tpl' => 'templates',
                'lib' => 'libraries',
                'cli' => 'cli',
                'mod' => 'modules'
            );

            $basePath = JPATH_SITE . '/' . $folders[$extensionType];

            $element = str_replace($extensionType . '_', '/', $element);
            if ($extensionType === 'plg') {
                $element = preg_replace('/_/', '/', $element, 1);
            } elseif ($extensionType === 'mod') {
                $element = preg_replace('/^\//', '/mod_', $element, 1);
            }

            $this->proIncludePath = $basePath . $element . '/library/pro/include.php';
        }

        return $this->proIncludePath;
    }
}
