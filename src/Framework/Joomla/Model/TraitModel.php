<?php
/**
 * @package   OSCampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2017-2023 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSCampus.
 *
 * OSCampus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSCampus is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSCampus.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\Model;

use Alledia\Framework\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\User\User;

defined('_JEXEC') or die();

trait TraitModel
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @var string[]
     */
    protected $accessList = [];

    /**
     * @return void
     * @throws \Exception
     */
    protected function setup()
    {
        $this->app = Factory::getApplication();
    }

    /**
     * Create a where clause of OR conditions for a text search
     * across one or more fields. Optionally accepts a text
     * search like 'id: #' if $idField is specified
     *
     * @param string          $text
     * @param string|string[] $fields
     * @param ?string         $idField
     *
     * @return string
     */
    public function whereTextSearch(string $text, $fields, ?string $idField = null): string
    {
        $text = trim($text);

        if ($idField && stripos($text, 'id:') === 0) {
            $id = (int)substr($text, 3);
            return $idField . ' = ' . $id;
        }

        if (is_string($fields)) {
            $fields = [$fields];
        }
        $searchText = Factory::getDbo()->quote('%' . $text . '%');

        $ors = [];
        foreach ($fields as $field) {
            $ors[] = $field . ' LIKE ' . $searchText;
        }

        if (count($ors) > 1) {
            return sprintf('(%s)', join(' OR ', $ors));
        }

        return array_pop($ors);
    }

    /**
     * Provide a generic access search for selected field
     *
     * @param string $field
     * @param ?User  $user
     *
     * @return string
     */
    public function whereAccess(string $field, ?User $user = null): string
    {
        $user = $user ?: Factory::getUser();
        if ($user->authorise('core.manage') == false) {
            $userId = $user->id;

            if (isset($this->accessList[$userId]) == false) {
                $this->accessList[$userId] = join(', ', array_unique($user->getAuthorisedViewLevels()));
            }

            if ($this->accessList[$userId]) {
                return sprintf($field . ' IN (%s)', $this->accessList[$userId]);
            }
        }

        return 'TRUE';
    }

    /**
     * @param string          $source
     * @param string|string[] $relations
     *
     * @return void
     */
    protected function garbageCollect(string $source, $relations)
    {
        $sourceParts = explode('.', $source);
        $sourceField = array_pop($sourceParts);
        $sourceTable = array_pop($sourceParts);

        if ($sourceTable && $sourceField) {
            $db = Factory::getDbo();

            if (is_string($relations)) {
                $relations = [$relations];
            }

            foreach ($relations as $target) {
                $targetParts = explode('.', $target);
                $targetField = array_pop($targetParts);
                $targetTable = array_pop($targetParts);

                if ($targetTable && $targetField) {
                    $query = $db->getQuery(true)
                        ->delete($db->quoteName('#__oscampus_' . $targetTable))
                        ->where(
                            sprintf(
                                '%s NOT IN (SELECT %s FROM %s)',
                                $db->quoteName($targetField),
                                $db->quoteName($sourceField),
                                $db->quoteName('#__oscampus_' . $sourceTable)
                            )
                        );
                }

                try {
                    $db->setQuery($query)->execute();

                } catch (\Throwable $error) {
                    $this->app->enqueueMessage($error->getMessage() . '<br>' . $query, 'error');
                }
            }
        }
    }
}
