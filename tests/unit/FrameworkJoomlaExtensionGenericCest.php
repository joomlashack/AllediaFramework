<?php
use \UnitTester;
use \JRegistry;
use \Codeception\Util\Stub;

class FrameworkJoomlaExtensionGenericCest
{
    public function _before(UnitTester $I)
    {
        $I->rollbackTransaction();
        $I->startTransaction();
    }

    public function _after(UnitTester $I)
    {
        $I->rollbackTransaction();
        $I->cleanupDatabase();
    }

    public function getElementToMatchDatabasePatternForComponent(UnitTester $I)
    {
        $instance = $I->constructDumbComponent();
        $element  = $instance->getElementToDb();

        $I->assertEquals('com_dumbextension', $element);
    }

    public function getElementToMatchDatabasePatternForModule(UnitTester $I)
    {
        $instance = $I->constructDumbModule();
        $element  = $instance->getElementToDb();

        $I->assertEquals('mod_dumbextension', $element);
    }

    public function getElementToMatchDatabasePatternForPlugin(UnitTester $I)
    {
        $instance = $I->constructDumbPlugin();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToMatchDatabasePatternForLibrary(UnitTester $I)
    {
        $instance = $I->constructDumbLibrary();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToMatchDatabasePatternForTemplate(UnitTester $I)
    {
        $instance = $I->constructDumbTemplate();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToMatchDatabasePatternForCLI(UnitTester $I)
    {
        $instance = $I->constructDumbCLI();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getInfoAfterConstructForComponent(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->constructDumbComponent();

        $type      = $I->getAttributeFromInstance($instance, 'type');
        $element   = $I->getAttributeFromInstance($instance, 'element');
        $folder    = $I->getAttributeFromInstance($instance, 'folder');
        $basePath  = $I->getAttributeFromInstance($instance, 'basePath');
        $namespace = $I->getAttributeFromInstance($instance, 'namespace');
        $id        = $I->getAttributeFromInstance($instance, 'id');
        $name      = $I->getAttributeFromInstance($instance, 'name');
        $enabled   = $I->getAttributeFromInstance($instance, 'enabled');
        $params    = $I->getAttributeFromInstance($instance, 'params');

        $I->assertEquals('component', $type);
        $I->assertEquals('dumbextension', $element);
        $I->assertEmpty('', $folder, 'There is no folder for components');
        $I->assertEquals(JPATH_SITE, $basePath);
        $I->assertEquals('DumbExtension', $namespace);
        $I->assertGreaterThan(0, $id);
        $I->assertEquals('COM_DUMBEXTENSION', $name);
        $I->assertTrue($enabled);
        $I->assertClassName('Joomla\Registry\Registry', $params);
    }

    public function getInfoAfterConstructForPlugin(UnitTester $I)
    {
        $I->addExtensionToDatabase('DumbExtension', 'plugin', 'dumbextension', 'system');

        $instance = Stub::construct(
            '\Alledia\Framework\Joomla\Extension\Generic',
            array(
                'namespace' => 'DumbExtension',
                'type'      => 'plugin',
                'folder'    => 'system'
            ),
            array(
                'getManifestPath' => $I->getExtensionMockPath('plg_system_dumbextension') . '/dumbextension.xml'
            )
        );

        $type      = $I->getAttributeFromInstance($instance, 'type');
        $element   = $I->getAttributeFromInstance($instance, 'element');
        $folder    = $I->getAttributeFromInstance($instance, 'folder');
        $basePath  = $I->getAttributeFromInstance($instance, 'basePath');
        $namespace = $I->getAttributeFromInstance($instance, 'namespace');
        $id        = $I->getAttributeFromInstance($instance, 'id');
        $name      = $I->getAttributeFromInstance($instance, 'name');
        $enabled   = $I->getAttributeFromInstance($instance, 'enabled');
        $params    = $I->getAttributeFromInstance($instance, 'params');

        $I->assertEquals('plugin', $type);
        $I->assertEquals('dumbextension', $element);
        $I->assertEquals('system', $folder);
        $I->assertEquals(JPATH_SITE, $basePath);
        $I->assertEquals('DumbExtension', $namespace);
        $I->assertGreaterThan(0, $id);
        $I->assertEquals('DumbExtension', $name);
        $I->assertTrue($enabled);
        $I->assertClassName('Joomla\Registry\Registry', $params);
    }

    public function checkIsEnabledWhenEnabled(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->constructDumbComponent(
            array(),
            array(
                'enabled' => true
            )
        );

        $I->assertTrue($instance->isEnabled(), 'The result of the method isEnabled');
    }

    public function checkIsNotEnabledWhenDisabled(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->constructDumbComponent(
            array(),
            array(
                'enabled' => false
            )
        );

        $I->assertFalse($instance->isEnabled(), 'The result of the method isEnabled');
    }

    public function getExtensionPathForComponent(UnitTester $I)
    {
        $instance = $I->constructDumbComponent();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/administrator/components/com_dumbextension', $path);
    }

    public function getExtensionPathForModule(UnitTester $I)
    {
        $instance = $I->constructDumbModule();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/modules/mod_dumbextension', $path);
    }

    public function getExtensionPathForPlugin(UnitTester $I)
    {
        $instance = $I->constructDumbPlugin();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/plugins/system/dumbextension', $path);
    }

    public function getExtensionPathForLibrary(UnitTester $I)
    {
        $instance = $I->constructDumbLibrary();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/libraries/dumbextension', $path);
    }

    public function getExtensionPathForTemplate(UnitTester $I)
    {
        $instance = $I->constructDumbTemplate();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/templates/dumbextension', $path);
    }

    public function getExtensionPathForCLI(UnitTester $I)
    {
        $instance = $I->constructDumbCLI();
        $path     = $instance->getExtensionPath();

        $I->assertEquals(JPATH_SITE . '/cli/dumbextension', $path);
    }

    public function getFullElementForComponent(UnitTester $I)
    {
        $instance    = $I->constructDumbComponent();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('com_dumbextension', $fullElement);
    }

    public function getFullElementForModule(UnitTester $I)
    {
        $instance    = $I->constructDumbModule();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('mod_dumbextension', $fullElement);
    }

    public function getFullElementForPlugin(UnitTester $I)
    {
        $instance    = $I->constructDumbPlugin();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('plg_system_dumbextension', $fullElement);
    }

    public function getFullElementForLibrary(UnitTester $I)
    {
        $instance    = $I->constructDumbLibrary();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('lib_dumbextension', $fullElement);
    }

    public function getFullElementForTemplate(UnitTester $I)
    {
        $instance    = $I->constructDumbTemplate();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('tpl_dumbextension', $fullElement);
    }

    public function getFullElementForCLI(UnitTester $I)
    {
        $instance    = $I->constructDumbCLI();
        $fullElement = $instance->getFullElement();

        $I->assertEquals('cli_dumbextension', $fullElement);
    }

    public function getElementToDbForComponent(UnitTester $I)
    {
        $instance = $I->constructDumbComponent();
        $element  = $instance->getElementToDb();

        $I->assertEquals('com_dumbextension', $element);
    }

    public function getElementToDbForModule(UnitTester $I)
    {
        $instance = $I->constructDumbModule();
        $element  = $instance->getElementToDb();

        $I->assertEquals('mod_dumbextension', $element);
    }

    public function getElementToDbForPlugin(UnitTester $I)
    {
        $instance = $I->constructDumbPlugin();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToDbForTemplate(UnitTester $I)
    {
        $instance = $I->constructDumbTemplate();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToDbForLibrary(UnitTester $I)
    {
        $instance = $I->constructDumbLibrary();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getElementToDbForCLI(UnitTester $I)
    {
        $instance = $I->constructDumbCLI();
        $element  = $instance->getElementToDb();

        $I->assertEquals('dumbextension', $element);
    }

    public function getManifestPathForComponent(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('com_dumbextension');
        $instance = $I->constructDumbComponent(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/dumbextension.xml', $path);
    }

    public function getManifestPathForModule(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('mod_dumbextension');
        $instance = $I->constructDumbModule(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/mod_dumbextension.xml', $path);
    }

    public function getManifestPathForPlugin(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('plg_system_dumbextension');
        $instance = $I->constructDumbModule(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/dumbextension.xml', $path);
    }

    public function getManifestPathForTemplate(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('tpl_dumbextension');
        $instance = $I->constructDumbTemplate(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/templateDetails.xml', $path);
    }

    public function getManifestPathForLibrary(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('lib_dumbextension');
        $instance = $I->constructDumbLibrary(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/dumbextension.xml', $path);
    }

    public function getManifestPathForCLI(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('cli_dumbextension');
        $instance = $I->constructDumbCLI(
            array(),
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $path = $instance->getManifestPath();

        $I->assertEquals($extensionPath . '/dumbextension.xml', $path);
    }

    public function getName(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->constructDumbComponent();

        $I->assertEquals('COM_DUMBEXTENSION', $instance->getName());
    }

    public function getId(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->constructDumbComponent();

        $I->assertGreaterThan(0, $instance->getId());
    }

    public function getManifestAsSimpleXML(UnitTester $I)
    {
        $instance = $I->makeDumbComponent();
        $manifest = $instance->getManifestAsSimpleXML(false);

        // Get the expected manifest
        $manifestPath = $I->getExtensionMockPath('com_dumbextension') . '/dumbextension.xml';
        $expected     = simplexml_load_file($manifestPath);

        $I->assertClassName('SimpleXMLElement', $manifest);
        $I->assertEquals($expected->asXML(), $manifest->asXML());
    }

    public function getManifest(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent();
        $manifest = $instance->getManifest(false);

        // Get the expected manifest
        $manifestPath = $I->getExtensionMockPath('com_dumbextension') . '/dumbextension.xml';
        $xml          = simplexml_load_file($manifestPath);
        $expected     = (object) json_decode(json_encode($xml));

        $I->assertEqualsSerializing($expected, $manifest);
    }

    public function getManifestForcing(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent();
        $instance->getManifest(false);

        // Update a manifest value to check if the force param is working
        $instance->manifest->name = 'NEW_NAME';

        // Get the manifest again forcing to refresh
        $instance->getManifest(true);

        $I->assertEquals('COM_DUMBEXTENSION', $instance->manifest->name);
    }

    public function getManifestWithoutForce(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent();
        $instance->getManifest(false);

        // Update a manifest value to check if the force param is working
        $instance->manifest->name = 'NEW_NAME';

        // Get the manifest again forcing to refresh
        $manifest = $instance->getManifest(false);

        $I->assertEquals('NEW_NAME', $manifest->name);
    }

    public function getConfigForValidConfigFile(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent(
            array(
                'getExtensionPath' => $I->getExtensionMockPath('com_dumbextension')
            )
        );
        $config = $instance->getConfig(false);

        $I->assertClassName('SimpleXMLElement', $config);
    }

    public function getConfigReturningFalseForNonExistentConfigFile(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        // Instantiate a component setting an invalid extension path to not find the config file
        $instance = $I->makeDumbComponent(
            array(
                'getExtensionPath' => __DIR__
            )
        );
        $config = $instance->getConfig(false);

        $I->assertNull($config);
    }

    public function getConfigForcing(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent(
            array(
                'getExtensionPath' => $I->getExtensionMockPath('com_dumbextension')
            )
        );
        $original = $instance->getConfig(false);

        $dumbconfig = $instance->config->xpath('//field[@id="dumbconfig1"]');
        $dumbconfig[0]->addAttribute('dumb_attribute', 17);

        $refreshed = $instance->getConfig(true);

        $I->assertClassName('SimpleXMLElement', $original);
        $I->assertClassName('SimpleXMLElement', $refreshed);
        $I->assertNotEquals($original->asXML(), $refreshed->asXML());
    }

    public function getConfigNotForcing(UnitTester $I)
    {
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $instance = $I->makeDumbComponent(
            array(
                'getExtensionPath' => $I->getExtensionMockPath('com_dumbextension')
            )
        );
        $original = $instance->getConfig(false);

        $dumbconfig = $instance->config->xpath('//field[@id="dumbconfig1"]');
        $dumbconfig[0]->addAttribute('dumb_attribute', 17);

        $instance->getConfig(false);

        $I->assertClassName('SimpleXMLElement', $original);
        $I->assertClassName('SimpleXMLElement', $instance->config);
        $I->assertEquals($original->asXML(), $instance->config->asXML());
    }

    public function getUpdateURL(UnitTester $I)
    {
        $siteId      = 99998;
        $name        = 'DumbExtension';
        $extensionId = 99999;
        $updateURL   = 'http://deploy.ostraining.com/client/update/free/stable/com_dumbextension';
        $I->addExtensionUpdateSiteToDatabase($siteId, $name, $extensionId, $updateURL);

        $instance = $I->makeDumbComponent(
            array(
                'id'    => $extensionId,
                'getId' => $extensionId
            )
        );

        $I->assertEquals($updateURL, $instance->getUpdateURL());
    }

    public function setUpdateURL(UnitTester $I)
    {
        $siteId            = 99998;
        $name              = 'DumbExtension';
        $extensionId       = 99999;
        $originalUpdateURL = 'http://deploy.ostraining.com/client/update/free/stable/com_dumbextension';
        $I->addExtensionUpdateSiteToDatabase($siteId, $name, $extensionId, $originalUpdateURL);

        $instance = $I->makeDumbComponent(
            array(
                'id'    => $extensionId,
                'getId' => $extensionId
            )
        );

        $newUpdateURL = 'http://deploy.ostraining.com/client/update/free/stable/com_dumbextension_new';
        $instance->setUpdateURL($newUpdateURL);

        $currentUpdateURL = $instance->getUpdateURL();

        $I->assertNotEquals($originalUpdateURL, $currentUpdateURL);
        $I->assertEquals($newUpdateURL, $currentUpdateURL);
    }

    public function storeParams(UnitTester $I)
    {
        $extensionId = $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', 'com_dumbextension');

        $params = new JRegistry;
        $params->set('param1', 02);
        $params->set('param2', 17);

        $instance = $I->makeDumbComponent(
            array(
                'id'     => $extensionId,
                'params' => $params
            )
        );

        $instance->storeParams();

        // Get the params from database
        $queriedParams = $I->loadResultFromDatabase("SELECT params FROM `#__extensions` WHERE extension_id = " . $extensionId);

        $I->assertEquals($params->toString(), $queriedParams);
    }

    public function getFooterMarkupForComponentWithFooterFromConfig(UnitTester $I)
    {
        $instance = $I->makeDumbComponent(
            array(
                'getExtensionPath' => $I->getExtensionMockPath('com_dumbextension')
            )
        );

        $footer = $instance->getFooterMarkup();

        $I->assertEquals('##alledia-footer-markup##', $footer);
    }

    public function getFooterMarkupForExtensionWithFooterFromManifest(UnitTester $I)
    {
        $manifestPath = $I->getExtensionMockPath('mod_dumbextension') . '/mod_dumbextension.xml';
        $manifest     = simplexml_load_file($manifestPath);

        $instance = $I->makeDumbModule(
            array(
                'getManifestAsSimpleXML' => $manifest
            )
        );

        $footer = $instance->getFooterMarkup();

        $I->assertEquals('##alledia-footer-markup##', $footer);
    }

    public function getEmptyFooterMarkupForExtensionWithoutFooter(UnitTester $I)
    {
        $manifestPath = $I->getExtensionMockPath('lib_dumbextension') . '/dumbextension.xml';
        $manifest     = simplexml_load_file($manifestPath);

        $instance = $I->makeDumbLibrary(
            array(
                'getManifestAsSimpleXML' => $manifest
            )
        );

        $footer = $instance->getFooterMarkup();

        $I->assertEmpty($footer);
    }
}
