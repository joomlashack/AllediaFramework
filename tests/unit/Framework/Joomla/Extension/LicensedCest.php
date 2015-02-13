<?php
use \UnitTester;
use \JRegistry;
use \Codeception\Util\Stub;

class LicensedCest
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

    public function isProForProComponent(UnitTester $I)
    {
        $instance = $I->makeDumbLicensedComponent(
            array(
                'license' => 'pro'
            )
        );

        $I->assertTrue($instance->isPro());
    }

    public function isNotProForFreeComponent(UnitTester $I)
    {
        $instance = $I->makeDumbLicensedComponent(
            array(
                'license' => 'free'
            )
        );

        $I->assertFalse($instance->isPro());
    }

    public function isFreeForFreeComponent(UnitTester $I)
    {
        $instance = $I->makeDumbLicensedComponent(
            array(
                'license' => 'free'
            )
        );

        $I->assertTrue($instance->isFree());
    }

    public function isNotFreeForProComponent(UnitTester $I)
    {
        $instance = $I->makeDumbLicensedComponent(
            array(
                'license' => 'pro'
            )
        );

        $I->assertFalse($instance->isFree());
    }

    public function getLibraryPath(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('com_dumbextension');
        $instance = $I->makeDumbLicensedComponent(
            array(
                'getExtensionPath' => $extensionPath
            )
        );

        $I->assertEquals($extensionPath . '/library', $instance->getLibraryPath());
    }

    public function getProLibraryPath(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('com_dumbextension') . '/admin/library';
        $instance = $I->makeDumbLicensedComponent(
            array(
                'getLibraryPath' => $extensionPath
            )
        );

        $I->assertEquals($extensionPath . '/Pro', $instance->getProLibraryPath());
    }

    public function loadLibraryFalseForComponentWithoutLibrary(UnitTester $I)
    {
        $extensionPath = $I->getExtensionMockPath('mod_dumbextension') . '/library';
        $instance = $I->makeDumbLicensedComponent(
            array(
                'getLibraryPath' => $extensionPath
            )
        );

        $I->assertFalse($instance->loadLibrary());
    }


    public function loadLibraryForFreeComponent(UnitTester $I)
    {
        $libraryPath = $I->getExtensionMockPath('com_dumbextension') . '/admin/library';
        $instance = $I->makeDumbLicensedComponent(
            array(
                'getLibraryPath' => $libraryPath,
                'namespace'      => 'DumbExtension'
            )
        );

        $loaded = $instance->loadLibrary();

        $library = new \Alledia\DumbExtension\Free\Library;

        $I->assertTrue($loaded);
        $I->assertIsObject($library);
        $I->assertClassName('Alledia\DumbExtension\Free\Library', $library);
    }

    public function loadLibraryForProComponent(UnitTester $I)
    {
        $libraryPath = $I->getExtensionMockPath('com_dumbextension_pro') . '/library';
        $instance = $I->makeDumbLicensedComponent(
            array(
                'getLibraryPath'    => $libraryPath,
                'namespace'         => 'DumbExtension',
                'getProLibraryPath' => $libraryPath . '/Pro'
            )
        );

        $loaded = $instance->loadLibrary();

        $freeLibrary = new \Alledia\DumbExtension\Free\FreeLib;
        $proLibrary  = new \Alledia\DumbExtension\Pro\ProLib;

        $I->assertTrue($loaded);
        $I->assertIsObject($freeLibrary);
        $I->assertIsObject($proLibrary);
        $I->assertClassName('Alledia\DumbExtension\Free\FreeLib', $freeLibrary);
        $I->assertClassName('Alledia\DumbExtension\Pro\ProLib', $proLibrary);
    }
}
