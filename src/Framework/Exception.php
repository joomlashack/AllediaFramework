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

defined('_JEXEC') or die();

class Exception extends \Exception
{
    /**
     * Set error message to include class::method() information. Could be used live
     * but very helpful during development.
     *
     * @return string
     */
    public function getTraceMessage()
    {
        $trace  = $this->getTrace();
        $caller = array_shift($trace);

        $result = '';
        if (!empty($caller['class'])) {
            $result .= $caller['class'] . '::';
        }
        if (!empty($caller['function'])) {
            $result .= $caller['function'] . '()';
        }

        return trim($result . ' ' . $this->message);
    }

    /**
     * Get single line listing of call stack
     *
     * @return array
     */
    public function getCallStack()
    {
        $trace = $this->getTrace();
        $stack = [];

        foreach ($trace as $caller) {
            $row = 'Line ' . (empty($caller['line']) ? '' : $caller['line'] . ' - ');
            if (!empty($caller['class'])) {
                $row .= $caller['class'] . '::';
            }
            if (!empty($caller['function'])) {
                $row .= $caller['function'] . '()';
            }

            if (!empty($caller['file'])) {
                $row .= ' [' . $caller['file'] . ']';
            }
            $stack[] = $row;
        }

        return $stack;
    }

    /**
     * Allow custom exceptions to be created. Note
     * that
     *
     * @param string $file
     *
     * @return void
     */
    public function setLocation(string $file, int $line)
    {
        $this->file = $file;
        $this->line = $line;
    }
}
