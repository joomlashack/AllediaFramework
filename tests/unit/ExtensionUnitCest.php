<?php
use \UnitTester;
use Alledia\Framework\Extension;
use \Codeception\Util\Stub;

class ExtensionUnitCest
{
    private $includeSubPath;

    public function _before()
    {
        $this->includeSubPath = '/library/pro';
    }

    public function _after()
    {
    }

    public function getProLibraryPathForComponent(UnitTester $I)
    {
        $extension = Stub::construct(
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
                'type'      => 'component'
            ),
            array('getDataFromDatabase' => false)
        );
        $path = $extension->getProLibraryPath();

        $I->assertEquals(JPATH_SITE . '/administrator/components/myextension' . $this->includeSubPath, $path);
    }

    public function getProLibraryPathForContentPlugin(UnitTester $I)
    {
        $extension = Stub::construct(
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyProExtension',
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
            'Alledia\Framework\Extension',
            array(
                'namespace' => 'MyFreeExtension',
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
