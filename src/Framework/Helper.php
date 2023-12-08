<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of AllediaFramework.
 *
 * AllediaFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * AllediaFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AllediaFramework.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework;

use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use ReflectionMethod;

defined('_JEXEC') or die();

abstract class Helper
{
    /**
     * @var int[]
     */
    protected static $errorConstants = null;

    /**
     * Return an array of Alledia extensions
     *
     * @param ?string $license
     *
     * @return object[]
     */
    public static function getAllediaExtensions(?string $license = ''): array
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('extension_id'),
                $db->quoteName('type'),
                $db->quoteName('element'),
                $db->quoteName('folder'),
            ])
            ->from('#__extensions')
            ->where([
                sprintf('%s LIKE %s', $db->quoteName('custom_data'), $db->quote('%"author":"Alledia"%')),
                sprintf('%s LIKE %s', $db->quoteName('custom_data'), $db->quote('%"author":"OSTraining"%')),
                sprintf('%s LIKE %s', $db->quoteName('custom_data'), $db->quote('%"author":"Joomlashack"%')),
                sprintf('%s LIKE %s', $db->quoteName('manifest_cache'), $db->quote('%"author":"Alledia"%')),
                sprintf('%s LIKE %s', $db->quoteName('manifest_cache'), $db->quote('%"author":"OSTraining"%')),
                sprintf('%s LIKE %s', $db->quoteName('manifest_cache'), $db->quote('%"author":"Joomlashack"%')),
            ], 'OR')
            ->group($db->quoteName('extension_id'));

        $rows = $db->setQuery($query)->loadObjectList();

        $extensions = [];
        foreach ($rows as $row) {
            if ($fullElement = ExtensionHelper::getFullElementFromInfo($row->type, $row->element, $row->folder)) {
                if ($extensionInfo = ExtensionHelper::getExtensionInfoFromElement($fullElement)) {
                    $extension = new Joomla\Extension\Licensed($extensionInfo['namespace'], $row->type, $row->folder);

                    if (
                        empty($license)
                        ||
                        ($license == 'pro' && $extension->isPro())
                        || ($license == 'free' && $extension->isFree())
                    ) {
                        $extensions[$row->extension_id] = $extension;
                    }
                }
            }
        }

        return $extensions;
    }

    /**
     * @return string
     */
    public static function getJoomlaVersionCssClass(): string
    {
        return sprintf('joomla%sx', Version::MAJOR_VERSION);
    }

    /**
     * Create class alias for classes that may not exist
     *
     * @param array $classes
     *
     * @return void
     */
    public static function createClassAliases(array $classes): void
    {
        foreach ($classes as $class => $classAlias) {
            if (class_exists($classAlias) == false) {
                class_alias($class, $classAlias);
            }
        }
    }

    /**
     * Create common database class aliases for classes that don't exist
     *
     * @return void
     */
    public static function createDatabaseClassAliases(): void
    {
        static::createClassAliases([
            \JDatabaseQuery::class  => DatabaseQuery::class,
            \JDatabaseDriver::class => DatabaseDriver::class,
        ]);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array  $params
     *
     * @return mixed
     */
    public static function callMethod(string $className, string $methodName, ?array $params = [])
    {
        $result = true;

        if (method_exists($className, $methodName)) {
            $method = new ReflectionMethod($className, $methodName);

            if ($method->isStatic()) {
                $result = call_user_func_array("{$className}::{$methodName}", $params);

            } else {
                // Check if we have a singleton class
                if (method_exists($className, 'getInstance')) {
                    $instance = $className::getInstance();

                } else {
                    $instance = new $className();
                }

                $result = call_user_func_array([$instance, $methodName], $params);
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    public static function createForm(string $name): Form
    {
        $form = new Form($name);
        $form->load('<?xml version="1.0" encoding="UTF-8"?><form/>');

        return $form;
    }

    /**
     * @param string  $name
     * @param ?string $appName
     * @param ?array  $options
     *
     * @return mixed
     */
    public static function getContentModel(string $name, ?string $appName = null, array $options = [])
    {
        return static::getJoomlaModel($name, 'ContentModel', 'com_content', $appName, $options);
    }

    /**
     * @param string  $name
     * @param ?string $appName
     * @param ?array  $options
     *
     * @return mixed
     */
    public static function getCategoryModel(string $name, ?string $appName = null, ?array $options = [])
    {
        return static::getJoomlaModel($name, 'CategoriesModel', 'com_categories', $appName, $options);
    }

    /**
     * @param string  $name
     * @param string  $prefix
     * @param string  $component
     * @param ?string $appName
     * @param ?array  $options
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getJoomlaModel(
        string $name,
        string $prefix,
        string $component,
        ?string $appName = null,
        ?array $options = []
    ) {
        $defaultApp = 'Site';
        $appNames   = [$defaultApp, 'Administrator'];

        $appName       = ucfirst($appName ?: $defaultApp);
        $appName       = in_array($appName, $appNames) ? $appName : $defaultApp;
        $basePath      = $appName == 'Administrator' ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $componentPath = $basePath . '/components/' . $component;

        Table::addIncludePath($componentPath . '/tables');
        Form::addFormPath($componentPath . '/forms');
        Form::addFormPath($componentPath . '/models/forms');
        Form::addFieldPath($componentPath . '/models/fields');
        Form::addFormPath($componentPath . '/model/form');
        Form::addFieldPath($componentPath . '/model/field');

        if (Version::MAJOR_VERSION < 4) {
            BaseDatabaseModel::addIncludePath($componentPath . '/models');

            $model = BaseDatabaseModel::getInstance($name, $prefix, $options);

        } else {
            $model = Factory::getApplication()->bootComponent($component)
                ->getMVCFactory()->createModel($name, $appName, $options);
        }

        return $model;
    }

    /**
     * For use by custom error handlers created using
     * set_error_handler() intended to catch php errors.
     *
     * @param int    $number
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return Exception
     */
    public static function errorToException(int $number, string $message, string $file, int $line): Exception
    {
        if (static::$errorConstants === null) {
            static::$errorConstants = [];

            $allErrors = get_defined_constants(true);
            foreach ($allErrors['Core'] as $name => $value) {
                if (strpos($name, 'E_') === 0) {
                    static::$errorConstants[$name] = $value;
                }
            }
        }

        $error = array_search($number, static::$errorConstants);

        $message = sprintf('%s - %s', $error === false ? $number : $error, $message);

        $exception = new Exception($message, 0);
        $exception->setLocation($file, $line);

        return $exception;
    }
}
