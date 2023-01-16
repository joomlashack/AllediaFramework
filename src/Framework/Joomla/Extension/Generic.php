<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of AllediaFramework.
 *
 * AllediaFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * AllediaFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AllediaFramework.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\Extension;

defined('_JEXEC') or die();

use JFormFieldCustomFooter;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;
use SimpleXMLElement;

/**
 * Generic extension class
 *
 * @todo : Make this class compatible with non-Alledia extensions
 */
class Generic
{
    /**
     * The extension namespace
     *
     * @var string
     */
    public $namespace = null;

    /**
     * The extension type
     *
     * @var string
     */
    public $type = null;

    /**
     * The extension id
     *
     * @var int
     */
    public $id = null;

    /**
     * The extension name
     *
     * @var string
     */
    public $name = null;

    /**
     * The extension params
     *
     * @var Registry
     */
    public $params = null;

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
     * @var string
     */
    protected $folder = null;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * The manifest information
     *
     * @var object
     */
    public $manifest = null;

    /**
     * The manifest information as SimpleXMLElement
     *
     * @var SimpleXMLElement
     */
    public $manifestXml = null;

    /**
     * The config information
     *
     * @var SimpleXMLElement
     */
    public $config = null;

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
        $this->basePath  = rtrim($basePath, '/\\');
        $this->namespace = $namespace;

        $this->getManifest();

        $this->getDataFromDatabase();
    }

    /**
     * Get information about this extension from the database
     *
     * @return void
     */
    protected function getDataFromDatabase()
    {
        $element = $this->getElementToDb();

        // Load the extension info from database
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('extension_id'),
                $db->quoteName('name'),
                $db->quoteName('enabled'),
                $db->quoteName('params')
            ])
            ->from('#__extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote($this->type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));

        if ($this->type === 'plugin') {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($this->folder));
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        if (is_object($row)) {
            $this->id      = $row->extension_id;
            $this->name    = $row->name;
            $this->enabled = (bool)$row->enabled;
            $this->params  = new Registry($row->params);

        } else {
            $this->id      = null;
            $this->name    = null;
            $this->enabled = false;
            $this->params  = new Registry();
        }
    }

    /**
     * Check if the extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get the path for the extension
     *
     * @return string The path
     */
    public function getExtensionPath()
    {
        $folders = [
            'component' => 'administrator/components/',
            'plugin'    => 'plugins/',
            'template'  => 'templates/',
            'library'   => 'libraries/',
            'cli'       => 'cli/',
            'module'    => 'modules/'
        ];

        $basePath = $this->basePath . '/' . $folders[$this->type];

        switch ($this->type) {
            case 'plugin':
                $basePath .= $this->folder . '/';
                break;

            case 'module':
                if (!preg_match('/^mod_/', $this->element)) {
                    $basePath .= 'mod_';
                }
                break;

            case 'component':
                if (!preg_match('/^com_/', $this->element)) {
                    $basePath .= 'com_';
                }
                break;
        }

        $basePath .= $this->element;

        return $basePath;
    }

    /**
     * Get the full element
     *
     * @return string The full element
     */
    public function getFullElement()
    {
        return Helper::getFullElementFromInfo($this->type, $this->element, $this->folder);
    }

    /**
     * Get the element to match the database records.
     * Only components and modules have the prefix.
     *
     * @return string The element
     */
    public function getElementToDb()
    {
        $prefixes = [
            'component' => 'com_',
            'module'    => 'mod_'
        ];

        $fullElement = '';
        if (array_key_exists($this->type, $prefixes)) {
            if (!preg_match('/^' . $prefixes[$this->type] . '/', $this->element)) {
                $fullElement = $prefixes[$this->type];
            }
        }

        $fullElement .= $this->element;

        return $fullElement;
    }

    /**
     * Get manifest path for this extension
     *
     * @return string
     */
    public function getManifestPath()
    {
        $extensionPath = $this->getExtensionPath();

        // Templates or extension?
        if ($this->type === 'template') {
            $fileName = 'templateDetails.xml';
        } else {
            $fileName = $this->element . '.xml';
        }

        $path = $extensionPath . "/{$fileName}";

        if (!is_file($path)) {
            $path = $extensionPath . "/{$this->getElementToDb()}.xml";
        }

        return $path;
    }

    /**
     * Get extension manifest as SimpleXMLElement
     *
     * @param bool $force If true, force to load the manifest, ignoring the cached one
     *
     * @return SimpleXMLElement
     */
    public function getManifestAsSimpleXML($force = false)
    {
        if (!isset($this->manifestXml) || $force) {
            $path = $this->getManifestPath();

            if (File::exists($path)) {
                $this->manifestXml = simplexml_load_file($path);
            } else {
                $this->manifestXml = false;
            }
        }

        return $this->manifestXml;
    }

    /**
     * Get extension information
     *
     * @param bool $force If true, force to load the manifest, ignoring the cached one
     *
     * @return object
     */
    public function getManifest($force = false)
    {
        if (!isset($this->manifest) || $force) {
            $xml = $this->getManifestAsSimpleXML($force);
            if (!empty($xml)) {
                $this->manifest = (object)json_decode(json_encode($xml));
            } else {
                $this->manifest = false;
            }
        }

        return $this->manifest;
    }

    /**
     * Get extension config file
     *
     * @param bool $force Force to reload the config file
     *
     * @return SimpleXMLElement
     */
    public function getConfig($force = false)
    {
        if (!isset($this->config) || $force) {
            $this->config = null;

            $path = $this->getExtensionPath() . '/config.xml';

            if (file_exists($path)) {
                $this->config = simplexml_load_file($path);
            }
        }

        return $this->config;
    }

    /**
     * Returns the update URL from database
     *
     * @return string
     */
    public function getUpdateURL()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('sites.location')
            ->from('#__update_sites AS sites')
            ->leftJoin('#__update_sites_extensions AS extensions ON (sites.update_site_id = extensions.update_site_id)')
            ->where('extensions.extension_id = ' . $this->id);

        return $db->setQuery($query)->loadResult();
    }

    /**
     * Set the update URL
     *
     * @param string $url
     */
    public function setUpdateURL($url)
    {
        $db = Factory::getDbo();

        // Get the update site id
        $join  = $db->quoteName('#__update_sites_extensions') . ' AS extensions '
            . 'ON (sites.update_site_id = extensions.update_site_id)';
        $query = $db->getQuery(true)
            ->select('sites.update_site_id')
            ->from($db->quoteName('#__update_sites') . ' AS sites')
            ->leftJoin($join)
            ->where('extensions.extension_id = ' . $this->id);

        $siteId = (int)$db->setQuery($query)->loadResult();

        if (!empty($siteId)) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__update_sites'))
                ->set($db->quoteName('location') . ' = ' . $db->quote($url))
                ->where($db->quoteName('update_site_id') . ' = ' . $siteId);

            $db->setQuery($query)->execute();
        }
    }

    /**
     * Store the params on the database
     *
     * @return void
     */
    public function storeParams()
    {
        $db = Factory::getDbo();

        $updateObject = (object)[
            'params'       => $this->params->toString(),
            'extension_id' => $this->id
        ];

        $db->updateObject('#__extensions', $updateObject, ['extension_id']);
    }

    /**
     * Get extension name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get extension id
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @TODO: Move to the licensed class?
     *
     * @return string
     */
    public function getFooterMarkup()
    {
        $manifest = $this->getManifestAsSimpleXML();

        if ($manifest->alledia) {
            $configPath = $this->getExtensionPath() . '/config.xml';
            if (File::exists($configPath)) {
                $config = $this->getConfig();

                if (is_object($config)) {
                    $footerElement = $config->xpath('//field[@type="customfooter"]');
                    $footerElement = reset($footerElement);
                }
            }

            if (empty($footerElement)) {
                if (is_object($manifest)) {
                    if ($footerElement = $manifest->xpath('//field[@type="customfooter"]')) {
                        $footerElement = reset($footerElement);

                    } elseif ($media = (string)$manifest->media['destination']) {
                        $customField = sprintf(
                            '<field type="customfooter" name="customfooter" media="%s"/>',
                            $media
                        );

                        $footerElement = new SimpleXMLElement($customField);
                    }
                }
            }

            if (empty($footerElement) == false) {
                if (class_exists('JFormFieldCustomFooter') === false) {
                    $classPath = $this->getExtensionPath() . '/form/fields/customfooter.php';
                    if (is_file($classPath)) {
                        require_once $classPath;
                    }
                }

                if (class_exists('JFormFieldCustomFooter')) {
                    $field                = new JFormFieldCustomFooter();
                    $field->fromInstaller = true;

                    return $field->getInputUsingCustomElement($footerElement);
                }
            }
        }

        return '';
    }

    /**
     * Returns the extension's version collected from the manifest file
     *
     * @return string The extension's version
     */
    public function getVersion()
    {
        if (!empty($this->manifest->version)) {
            return $this->manifest->version;
        }

        return null;
    }
}
