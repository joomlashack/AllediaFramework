<?php
use \AcceptanceTester;

require_once ALLEDIA_BUILDER_PATH . '/src/tests_extension/tests/acceptance/ExtensionInstallerCest.php';

class AAAExtensionInstallerCest extends ExtensionInstallerCest
{
    public function _before(AcceptanceTester $I)
    {
        parent::_before($I);
    }

    public function _after(AcceptanceTester $I)
    {
        parent::_after($I);
    }
}
