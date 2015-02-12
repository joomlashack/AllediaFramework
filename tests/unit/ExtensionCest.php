<?php
use \UnitTester;
use \Codeception\Util\Stub;
use \Alledia\Framework\Extension;

class ExtensionCest
{
    private $includeSubPath = '/library/pro';

    public function _before()
    {
    }

    public function _after()
    {
    }

    public function getProLibraryPathForComponent(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockComponentFree',
                'type'      => 'component'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/administrator/components/mockcomponentfree' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForContentPlugin(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockContentPlugin',
                'type'      => 'plugin',
                'folder'    => 'content'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/plugins/content/myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForSystemPlugin(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockSystemPlugin',
                'type'      => 'plugin',
                'folder'    => 'system'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/plugins/system/myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForModule(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockModule',
                'type'      => 'module'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/modules/mod_myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForLibrary(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockLibrary',
                'type'      => 'library'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/libraries/myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForTemplates(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockTemplate',
                'type'      => 'template'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/templates/myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForCli(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockCli',
                'type'      => 'cli'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/cli/myextension' . $this->includeSubPath, $path);
    }

    public function getIsProFromProComponent(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockComponentPro',
                'type'      => 'component',
                'folder'    => null,
                'basePath'  => __DIR__ . '/../_support/joomla'
            ),
            array('getDataFromDatabase' => false)
        );
        $extension->getProLibraryPath();

        $I->assertTrue($extension->isPro());
    }

    public function getIsProFromFreeComponent(UnitTester $I)
    {
        $extension = Stub::construct(
            '\Alledia\Framework\Extension',
            array(
                'namespace' => 'MockComponentFree',
                'type'      => 'component',
                'folder'    => null,
                'basePath'  => __DIR__ . '/../_support/joomla'
            ),
            array('getDataFromDatabase' => false)
        );
        $extension->getProLibraryPath();

        $I->assertFalse($extension->isPro());
    }
}
