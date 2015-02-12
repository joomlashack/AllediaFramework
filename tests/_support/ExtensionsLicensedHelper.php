<?php
namespace Codeception\Module;

use \Codeception\Util\Stub;


class ExtensionsLicensedHelper extends \Codeception\Module
{
    public function constructDumbLicensedComponent($params = array(), $override = array())
    {
        $defaultParams = array(
            'namespace' => 'DumbExtension',
            'type'      => 'component'
        );

        $defaultOverride = array(
            'getManifestPath' => $this->getExtensionMockPath('com_dumbextension') . '/dumbextension.xml'
        );

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Licensed',
            array_merge($defaultParams, $params),
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    public function makeDumbLicensedComponent($override = array())
    {
        $defaultOverride = array(
            'getManifestPath' => $this->getExtensionMockPath('com_dumbextension') . '/dumbextension.xml'
        );

        $instance = Stub::make(
            '\Alledia\Framework\Joomla\Extension\Licensed',
            array_merge($defaultOverride, $override)
        );

        return $instance;
    }

    protected function getExtensionMockPath($element)
    {
        return $this->getModule('ExtensionsGenericHelper')->getExtensionMockPath($element);
    }
}
