<?php
/**
 * @package   AllediaFramework
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013-2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia;

defined('_JEXEC') or die();

class Extension extends \JObject
{
    /**
     * The extension namespace
     *
     * @var string
     */
    protected $namespace;

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
     * The extension enable state
     *
     * @var bool
     */
    protected $enabled;

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
     * The path for the pro library
     *
     * @var string
     */
    protected $proLibraryPath;

    /**
     * Class constructor, set the extension type.
     *
     * @param string $namespace The element of the extension
     * @param string $type      The type of extension
     * @param string $folder    The folder for plugins (only)
     */
    public function __construct($namespace, $type, $folder = '', $basePath = JPATH_SITE)
    {
        $this->type      = $type;
        $this->element   = strtolower($namespace);
        $this->folder    = $folder;
        $this->basePath  = $basePath;
        $this->namespace = $namespace;

        $this->getProLibraryPath();

        $this->license = file_exists($this->proLibraryPath) ? 'pro' : 'free';

        $this->getDataFromDatabase();
    }

    /**
     * Get information about this extension from the databae
     */
    protected function getDataFromDatabase()
    {
        // Load the extension info from database
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select(array(
                $db->quoteName('name'),
                $db->quoteName('enabled'),
                $db->quoteName('params')
            ))
            ->from('#__extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote($this->type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->element));

        if ($this->type === 'plugin') {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($this->folder));
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        if (!is_object($row)) {
            throw new \Exception("Extension not found: {$this->element}, {$this->type}, {$this->folder}");
        }

        $this->name = $row->name;
        $this->enabled = (bool) $row->enabled;
        $this->params = new \JRegistry($row->params);
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
     * Check if the extension is enabled
     *
     * @return boolean True for enabled
     */
    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * Get the include path for the include on the pro library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getProLibraryPath()
    {
        if (empty($this->proLibraryPath)) {
            $basePath = '';

            $folders = array(
                'component' => 'administrator/components/',
                'plugin'    => 'plugins/',
                'template'  => 'templates/',
                'library'   => 'libraries/',
                'cli'       => 'cli/',
                'module'    => 'modules/'
            );

            $basePath = $this->basePath . '/' . $folders[$this->type];

            if ($this->type === 'plugin') {
                $basePath .= $this->folder . '/' . $this->element;
            } elseif ($this->type === 'module') {
                $basePath .= 'mod_' . $this->element;
            } else {
                $basePath .= $this->element;
            }

            $this->proLibraryPath = $basePath . '/library/pro';
        }

        return $this->proLibraryPath;
    }

    public function loadProLibrary()
    {
        if ($this->isPro()) {
            $proLibraryPath = $this->getProLibraryPath();

            if (!file_exists($proLibraryPath)) {
                throw new \Exception("Pro library not found: {$this->extension->type}, {$this->extension->element}");
            }

            // Setup autoloaded libraries
            $loader = new \Psr4AutoLoader();
            $loader->register();
            $loader->addNamespace($this->namespace . 'Pro', $proLibraryPath);

            return true;
        }

        return false;
    }

    /**
     * Get the full element
     *
     * @return string The full element
     */
    public function getFullElement()
    {
        $prefixes = array(
            'component' => 'com',
            'plugin'    => 'plg',
            'template'  => 'tpl',
            'library'   => 'lib',
            'cli'       => 'cli',
            'module'    => 'mod'
        );

        $fullElement = $prefixes[$this->type] . '_';

        if ($this->type === 'plugin') {
            $fullElement .= '_' . $this->folder;
        }

        return $this->fullElement;
    }
}
