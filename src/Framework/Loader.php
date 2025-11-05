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

use Exception;
use Joomla\CMS\Log\Log;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

abstract class Loader
{
    /**
     * @var bool
     */
    protected static bool $logRegistered = false;

    /**
     * Safelly include a PHP file, making sure it exists before import.
     *
     * This method will register a log message and display a warning for admins
     * in case the file is missed.
     *
     * @param string $path The file path you want to include
     *
     * @return bool True, if the file exists and was loaded well.
     * @throws Exception
     */
    public static function includeFile(string $path): bool
    {
        if (static::$logRegistered == false) {
            Log::addLogger(
                ['text_file' => 'allediaframework.loader.errors.php'],
                Log::ALL,
                ['allediaframework']
            );

            static::$logRegistered = true;
        }

        // Check if the file doesn't exist
        if (is_file($path) == false) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $file      = $backtrace[0]['file'] ?? null;
            $line      = $backtrace[0]['line'] ?? null;

            // Register the log
            $logMsg = sprintf('Required file is missing: %s (%s:%s)', $path, $file, $line);
            Log::add($logMsg, Log::ERROR, 'allediaframework');

            // Warn admin users
            $app = Factory::getApplication();
            if ($app->isClient('administrator')) {
                $app->enqueueMessage(
                    'Joomlashack Framework Loader detected that a required file was not found! Please, check the logs.',
                    'error'
                );
            }

            // Stand up a flag to warn a required file is missed
            if (!defined('ALLEDIA_FRAMEWORK_MISSED_FILE')) {
                define('ALLEDIA_FRAMEWORK_MISSED_FILE', true);
            }

            return false;
        }

        include_once $path;

        return true;
    }
}
