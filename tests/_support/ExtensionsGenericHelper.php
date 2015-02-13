<?php
namespace Codeception\Module;

use \Codeception\Util\Stub;


class ExtensionsGenericHelper extends \Codeception\Module
{
    public function constructDumbGenericComponent($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'component'
        );

        $defaultOverride = array(
            'getManifestPath' => $this->getExtensionMockPath('com_dumbextension') . '/dumbextension.xml'
        );

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function makeDumbGenericComponent($override = array())
    {
        $defaultOverride = array(
            'getManifestPath' => $this->getExtensionMockPath('com_dumbextension') . '/admin/dumbextension.xml'
        );

        $instance = Stub::make(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function makeDumbGenericModule($override = array())
    {
        $defaultOverride = array(
            'getExtensionPath' => $this->getExtensionMockPath('mod_dumbextension'),
            'getManifestPath'  => $this->getExtensionMockPath('mod_dumbextension') . '/mod_dumbextension.xml'
        );

        $instance = Stub::make(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function makeDumbGenericLibrary($override = array())
    {
        $defaultOverride = array(
            'getExtensionPath' => $this->getExtensionMockPath('lib_dumbextension'),
            'getManifestPath'  => $this->getExtensionMockPath('lib_dumbextension') . '/dumbextension.xml'
        );

        $instance = Stub::make(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function constructDumbGenericModule($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'module'
        );

        $defaultOverride = array();

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function constructDumbGenericPlugin($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'plugin',
            'folder'    => 'system'
        );

        $defaultOverride = array();

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function constructDumbGenericLibrary($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'library'
        );

        $defaultOverride = array();

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function constructDumbGenericTemplate($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'template'
        );

        $defaultOverride = array();

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function constructDumbGenericCLI($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'cli'
        );

        $defaultOverride = array();

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function getExtensionMockPath($element)
    {
        return realpath(__DIR__ . '/../_support/mock/' . $element);
    }
}
