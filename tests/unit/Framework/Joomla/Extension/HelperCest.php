<?php
use \UnitTester;
use \JRegistry;
use \Codeception\Util\Stub;


class HelperCest
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

    public function getExtensionInfoFromElementForComponent(UnitTester $I)
    {
        $element = 'com_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('component', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertNull($info['group']);
        $I->assertEquals('com', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionInfoFromElementForPlugin(UnitTester $I)
    {
        $element = 'plg_system_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('plugin', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertEquals('system', $info['group']);
        $I->assertEquals('plg', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionInfoFromElementForModule(UnitTester $I)
    {
        $element = 'mod_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('module', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertNull($info['group']);
        $I->assertEquals('mod', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionInfoFromElementForTemplate(UnitTester $I)
    {
        $element = 'tpl_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('template', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertNull($info['group']);
        $I->assertEquals('tpl', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionInfoFromElementForLibrary(UnitTester $I)
    {
        $element = 'lib_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('library', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertNull($info['group']);
        $I->assertEquals('lib', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionInfoFromElementForCLI(UnitTester $I)
    {
        $element = 'cli_dumbextension';
        $info = Alledia\Framework\Joomla\Extension\Helper::getExtensionInfoFromElement($element);

        $I->assertIsArray($info);
        $I->assertEquals('cli', $info['type']);
        $I->assertEquals('dumbextension', $info['name']);
        $I->assertNull($info['group']);
        $I->assertEquals('cli', $info['prefix']);
        $I->assertEquals('dumbextension', $info['namespace']);
    }

    public function getExtensionForElement(UnitTester $I)
    {
        $element = 'com_dumbextension';
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        $extension = Alledia\Framework\Joomla\Extension\Helper::getExtensionForElement($element);

        $I->removeMockComponentFromJoomla($element);

        $I->assertIsObject($extension);
        $I->assertClassName('Alledia\Framework\Joomla\Extension\Licensed', $extension);
    }

    public function loadLibrary(UnitTester $I)
    {
        $element = 'com_dumbextension';
        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        $loaded = Alledia\Framework\Joomla\Extension\Helper::loadLibrary($element);
        $library = new Alledia\DumbExtension\Free\Library;

        $I->assertTrue($loaded);
        $I->assertIsObject($library);
        $I->assertClassName('Alledia\DumbExtension\Free\Library', $library);

        $I->removeMockComponentFromJoomla($element);
    }

    public function getFooterMarkupPassingElementAsString(UnitTester $I)
    {
        $element = 'com_dumbextension';

        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        $footer = Alledia\Framework\Joomla\Extension\Helper::getFooterMarkup($element);

        $I->assertEquals('##alledia-footer-markup##', $footer);

        $I->removeMockComponentFromJoomla($element);
    }

    public function getFooterMarkupPassingElementAsObject(UnitTester $I)
    {
        $element = 'com_dumbextension';
        $dumbFooter = '##alledia-footer-markup##';

        $instance = $I->makeDumbLicensedComponent(
            array(
                'getFooterMarkup' => $dumbFooter
            )
        );

        $footer = Alledia\Framework\Joomla\Extension\Helper::getFooterMarkup($instance);

        $I->assertEquals($dumbFooter, $footer);
    }
}
