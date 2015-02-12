<?php
namespace Codeception\Module;

use JFactory;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class JoomlaDboHelper extends \Codeception\Module
{
    private $siteIds = array();

    public function startTransaction()
    {
        $db = JFactory::getDbo();
        $db->transactionStart();
    }

    public function rollbackTransaction()
    {
        $db = JFactory::getDbo();
        $db->transactionRollback();
    }

    /**
     * For the database cleanup because failed tests can left some registers
     * because the transaction is not rolledback.
     *
     * @return [type] [description]
     */
    public function cleanupDatabase()
    {
        $db = JFactory::getDbo();

        $db->setQuery("DELETE FROM `#__extensions`
                        WHERE element = \"com_dumbextension\"");
        $db->execute();

        if (!empty($this->siteIds)) {
            foreach ($this->siteIds as $id) {
                $db->setQuery("DELETE FROM `#__update_sites`
                        WHERE update_site_id = \"{$id}\"");
                $db->execute();

                $db->setQuery("DELETE FROM `#__update_sites_extensions`
                        WHERE update_site_id = \"{$id}\"");
                $db->execute();
            }
        }
    }

    public function addExtensionToDatabase($name, $type, $element, $folder = '')
    {
        $db = JFactory::getDbo();
        $db->setQuery("INSERT INTO `#__extensions`
                        (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
                        VALUES
                            (NULL, '{$name}', '{$type}', '{$element}', '{$folder}', 1, 1, 0, 0, '{\"name\":\"{$name}\",\"type\":\"{$type}\",\"creationDate\":\"November 10, 2014\",\"author\":\"Alledia\",\"copyright\":\"Copyright (C) 2014 Alledia.com. All rights reserved.\",\"authorEmail\":\"hello@alledia.com\",\"authorUrl\":\"https:\\/\\/www.alledia.com\",\"version\":\"1.1.1-beta.2\",\"description\":\"Dumb extension for tests...\",\"group\":\"\"}', '{}', '{\"author\":\"Alledia\"}', '', 0, '0000-00-00 00:00:00', 0, 0);");

        $db->execute();

        return $db->insertid();
    }

    public function addExtensionUpdateSiteToDatabase($siteId, $name, $extensionId, $updateURL)
    {
        $this->siteIds[] = $siteId;

        $db = JFactory::getDbo();

        $db->setQuery("INSERT INTO `#__update_sites`
                        (`update_site_id`, `name`, `type`, `location`, `enabled`, `last_check_timestamp`, `extra_query`)
                        VALUES
                            ({$siteId}, '{$name}', 'extension', '{$updateURL}', 1, 1423588650, '');");
        $db->execute();

        $db->setQuery("INSERT INTO `#__update_sites_extensions`
                        (`update_site_id`, `extension_id`)
                        VALUES
                            ({$siteId}, {$extensionId});");
        $db->execute();
    }

    public function loadResultFromDatabase($sql)
    {
        $db = JFactory::getDbo();
        $db->setQuery($sql);

        return $db->loadResult();
    }

    public function loadObjectFromDatabase($sql)
    {
        $db = JFactory::getDbo();
        $db->setQuery($sql);

        return $db->loadObject();
    }

    public function loadObjectListFromDatabase($sql)
    {
        $db = JFactory::getDbo();
        $db->setQuery($sql);

        return $db->loadObjectList();
    }

    public function insertIntoDatabase($sql)
    {
        $db = JFactory::getDbo();
        $db->setQuery($sql);
        $db->execute();

        return $db->insertid();
    }
}
