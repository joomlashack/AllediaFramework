<?php
/**
 * @package   AllediaFramework
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace Alledia\Framework;

defined('_JEXEC') or die();

class Extension extends Object
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
     * The extension id
     *
     * @var int
     */
    protected $id;

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
     * The path for the free library
     *
     * @var string
     */
    protected $libraryPath;

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
        $this->element   = $this->removeExtensionTypePrefixFromElement(strtolower($namespace));
        $this->folder    = $folder;
        $this->basePath  = $basePath;
        $this->namespace = $namespace;

        $this->getLibraryPath();
        $this->getProLibraryPath();

        $this->license = file_exists($this->proLibraryPath) ? 'pro' : 'free';

        $this->getDataFromDatabase();
    }

    /**
     * Get information about this extension from the databae
     */
    protected function getDataFromDatabase()
    {
        $element = $this->getElementToDb($this->type, $this->element);

        // Load the extension info from database
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select(array(
                $db->quoteName('extension_id'),
                $db->quoteName('name'),
                $db->quoteName('enabled'),
                $db->quoteName('params')
            ))
            ->from('#__extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote($this->type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));

        if ($this->type === 'plugin') {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($this->folder));
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        if (!is_object($row)) {
            throw new Exception("Extension not found: {$this->element}, {$this->type}, {$this->folder}");
        }

        $this->id = $row->extension_id;
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
     * Get the path for the extension
     *
     * @return string The path
     */
    public function getExtensionPath()
    {
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

        return $basePath;
    }

    /**
     * Get the include path for the include on the free library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getLibraryPath()
    {
        if (empty($this->libraryPath)) {
            $basePath = $this->getExtensionPath();

            $this->libraryPath = $basePath . '/library';
        }

        return $this->libraryPath;
    }

    /**
     * Get the include path for the include on the pro library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getProLibraryPath()
    {
        if (empty($this->proLibraryPath)) {
            $basePath = $this->getLibraryPath();

            $this->proLibraryPath = $basePath . '/Pro';
        }

        return $this->proLibraryPath;
    }

    /**
     * Loads the library, if existent (including the Pro Library)
     *
     * @return bool
     */
    public function loadLibrary()
    {
        $libraryPath = $this->getLibraryPath();

        // If we have a library path, lets load it
        if (file_exists($libraryPath)) {

            if ($this->isPro()) {
                // Check if the pro library exists
                if (!file_exists($this->getProLibraryPath())) {
                    throw new Exception("Pro library not found: {$this->extension->type}, {$this->extension->element}");
                }
            }
            // Setup autoloaded libraries
            $loader = new \AllediaPsr4AutoLoader();
            $loader->register();
            $loader->addNamespace('Alledia\\' . $this->namespace, $libraryPath);

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

    /**
     * Get the element to match the database records.
     * Only components and modules have the prefix.
     *
     * @param  string $type
     * @param  string $element
     * @return string The element
     */
    public function getElementToDb($type, $element)
    {
        $prefixes = array(
            'component' => 'com_',
            'module'    => 'mod_'
        );

        $fullElement = '';
        if (array_key_exists($type, $prefixes)) {
            $fullElement = $prefixes[$type];
        }

        $fullElement .= $element;

        return $fullElement;
    }

    /**
     * Get extension information
     *
     * @return JRegistry
     */
    public function getInfo()
    {
        $info = new \JRegistry();

        jimport('joomla.installer.installer');

        $installer = \JInstaller::getInstance();
        $installer->setPath('source', $this->getExtensionPath());
        $installer->getManifest();
        $manifestPath = $installer->getPath('manifest');

        if (file_exists($manifestPath)) {
            $xml = \JFactory::getXML($manifestPath);

            foreach ($xml->children() as $e) {
                if (!$e->children()) {
                    $info->set($e->getName(), (string)$e);
                }
            }
        }

        return $info;
    }

    /**
     * Returns the update URL from database
     *
     * @return string
     */
    public function getUpdateURL()
    {
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('sites.location')
            ->from('#__update_sites AS sites')
            ->join('LEFT', '#__update_sites_extensions AS extensions ON (sites.update_site_id = extensions.update_site_id)')
            ->where('extensions.extension_id = ' . $this->id);
        $db->setQuery($query);
        $row = $db->loadResult();

        return $row;
    }

    /**
     * Set the update URL
     *
     * @param string $url
     */
    public function setUpdateURL($url)
    {
        $db = \JFactory::getDbo();

        // Get the update site id
        $join = $db->quoteName('#__update_sites_extensions') . ' AS extensions '
            . 'ON (sites.update_site_id = extensions.update_site_id)';
        $query = $db->getQuery(true)
            ->select('sites.update_site_id')
            ->from($db->quoteName('#__update_sites') . ' AS sites')
            ->join('LEFT', $join)
            ->where('extensions.extension_id = ' . $this->id);
        $db->setQuery($query);
        $siteId = (int) $db->loadResult();

        if (!empty($siteId)) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__update_sites'))
                ->set($db->quoteName('location') . ' = ' . $db->quote($url))
                ->where($db->quoteName('update_site_id') . ' = ' . $siteId);
            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * Remove the extension type prefix from the string
     *
     * @param  string $element
     * @return string
     */
    protected static function removeExtensionTypePrefixFromElement($element)
    {
        $prefixes = array('com_', 'plg_', 'tpl_', 'lib_', 'cli_', 'mod_');

        foreach ($prefixes as $prefix) {
            $element = preg_replace('/^' . $prefix . '/i', '', $element);
        }

        return $element;
    }
}
