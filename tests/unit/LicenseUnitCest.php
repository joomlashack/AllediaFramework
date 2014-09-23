<?php
use \UnitTester;
use Alledia\License;

class LicenseUnitCest
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
        $license = new License('com_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/administrator/components/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForContentPlugin(UnitTester $I)
    {
        $license = new License('plg_content_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/plugins/content/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForSystemPlugin(UnitTester $I)
    {
        $license = new License('plg_system_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/plugins/system/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForModule(UnitTester $I)
    {
        $license = new License('mod_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/modules/mod_myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForLibrary(UnitTester $I)
    {
        $license = new License('lib_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/libraries/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForTemplates(UnitTester $I)
    {
        $license = new License('tpl_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/templates/myextension' . $this->includeSubPath, $path);
    }

    public function getProIncludePathForCli(UnitTester $I)
    {
        $license = new License('cli_myextension');
        $path = $license->getProIncludePathForElement();

        $I->assertEquals(JPATH_SITE . '/cli/myextension' . $this->includeSubPath, $path);
    }
}
