<?php
use \UnitTester;
use Alledia\Extension;
use \Codeception\Util\Stub;

class ExtensionUnitCest
{
    private $includeSubPath;

    public function _before()
    {
        $this->includeSubPath = '/library/pro/include.php';
    }

    public function _after()
    {
    }

    public function getProIncludePathForComponent(UnitTester $I)
    {
        $extension = new Extension('component', 'myextension');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/administrator/components/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForContentPlugin(UnitTester $I)
    {
        $extension = new Extension('plugin', 'myextension', 'content');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/plugins/content/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForSystemPlugin(UnitTester $I)
    {
        $extension = new Extension('plugin', 'myextension', 'system');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/plugins/system/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForModule(UnitTester $I)
    {
        $extension = new Extension('module', 'myextension');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/modules/mod_myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForLibrary(UnitTester $I)
    {
        $extension = new Extension('library', 'myextension');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/libraries/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForTemplates(UnitTester $I)
    {
        $extension = new Extension('template', 'myextension');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/templates/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForCli(UnitTester $I)
    {
        $extension = new Extension('cli', 'myextension');
        $path = $extension->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/cli/myextension' . $this->includeSubPath, $path);
    }

    public function getIsProFromProComponent(UnitTester $I)
    {
        $extension = new Extension('component', 'myproextension', null, __DIR__ . '/../_support/joomla');
        $extension->getProIncludePathForElement();

        $I->assertTrue($extension->isPro());
    }

    public function getIsProFromFreeComponent(UnitTester $I)
    {
        $extension = new Extension('component', 'myfreeextension', null, __DIR__ . '/../_support/joomla');
        $extension->getProIncludePathForElement();

        $I->assertFalse($extension->isPro());
    }
}
