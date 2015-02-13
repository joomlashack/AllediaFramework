<?php
use \UnitTester;
use \Codeception\Util\Stub;

require_once __DIR__ . '/../../../../_support/mock/plg_system_dumbextension/dumbextension.php';


class AbstractPluginCest
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

    public function initAndLoadExtension(UnitTester $I)
    {
        // $defaultOverride = array(
        //     'loadLanguage' => true
        // );

        // $instance = Stub::make(
        //     'PlgSystemDumbExtension',
        //     $defaultOverride
        // );

        // $instance->executeInit();


        // $I->assertIsObject($instance);
    }
}
