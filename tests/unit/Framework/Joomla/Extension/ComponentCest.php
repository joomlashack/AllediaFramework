<?php
use \UnitTester;
use \Codeception\Util\Stub;
use \JRequest;
use \JFactory;


class ComponentCest
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

    public function constructAndLoadLibrary(UnitTester $I)
    {
        $element = 'com_dumbextension';

        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        $component = new Alledia\Framework\Joomla\Extension\Component('DumbExtension');
        $library   = new Alledia\DumbExtension\Free\Library;

        $I->assertIsObject($component);
        $I->assertIsObject($library);

        $I->removeMockComponentFromJoomla($element);
    }

    public function loadController(UnitTester $I)
    {
        $element = 'com_dumbextension';

        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        if (!defined('JPATH_COMPONENT')) {
            define('JPATH_COMPONENT', JPATH_SITE . '/components/com_dumbextension');
        }

        $component = new Alledia\Framework\Joomla\Extension\Component('DumbExtension');
        $component->loadController();

        $controller = $I->getAttributeFromInstance($component, 'controller');

        $I->assertIsObject($component);
        $I->assertIsObject($controller);
        $I->assertClassName('DumbExtensionController', $controller);

        $I->removeMockComponentFromJoomla($element);
    }

    public function executeTask(UnitTester $I)
    {
        $task = 'dumb_task';
        if (version_compare(JVERSION, '3.0', 'lt')) {
            JRequest::set('task', $task);
        } else {
            $app = JFactory::getApplication();
            $app->input->set('task', $task);
        }

        $element = 'com_dumbextension';

        $I->addExtensionToDatabase('COM_DUMBEXTENSION', 'component', $element);
        $I->copyMockComponentToJoomla($element);

        if (!defined('JPATH_COMPONENT')) {
            define('JPATH_COMPONENT', JPATH_SITE . '/components/com_dumbextension');
        }

        $component = new Alledia\Framework\Joomla\Extension\Component('DumbExtension');
        $component->loadController();
        $component->executeTask();

        $controller = $I->getAttributeFromInstance($component, 'controller');

        $I->assertIsObject($component);
        $I->assertIsObject($controller);
        $I->assertClassName('DumbExtensionController', $controller);
        $I->assertEquals($task, $controller->executedTask);
        $I->assertTrue($controller->redirected);

        $I->removeMockComponentFromJoomla($element);
    }
}
