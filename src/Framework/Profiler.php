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

class Profiler
{
    /**
     * @var int
     */
    protected $startTime = 0;

    /**
     * @var int
     */
    protected $initialMemory = 0;

    /**
     * @var int
     */
    protected $maxLength = 80;

    /**
     * @var int
     */
    protected $lastMemory = 0;

    /**
     * @return void
     */
    public function start()
    {
        $this->initialMemory = memory_get_usage();
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function step($label = null)
    {
        $this->startStep($label);
        $this->endStep();
    }

    /**
     * @return void
     */
    public function echoData()
    {
        echo "\n";
        $total    = memory_get_usage() - $this->initialMemory;
        $data     = '==== Mem: ' . number_format($total, 0, '.', ',') . ' bytes';
        $diff     = $total - $this->lastMemory;
        $peak     = memory_get_peak_usage();
        $operator = '';

        echo $data;

        if ($diff != 0) {
            $operator = $diff > 0 ? '+' : '-';
        }

        echo '    diff: ' . $operator . number_format(abs($diff), 0, '.', ',') . ' bytes'
            . '    peak: ' . number_format($peak, '0', '.', ',') . ' bytes';

        $this->lastMemory = $total;
        echo "\n";
    }

    /**
     * @param string $label
     */
    public function startStep($label = null)
    {
        echo "\n";
        $this->printHeader($label);
        $this->echoData();
    }

    /**
     * @return void
     */
    public function endStep()
    {
        $this->echoData();
        $this->printSeparator();
        echo "\n";
    }

    /**
     * @param string $label
     * @param int    $leftPadding
     *
     * @return void
     */
    protected function printHeader($label = null, $leftPadding = 4)
    {
        if (!is_null($label)) {
            $length = $leftPadding;

            echo str_repeat('=', $length);

            echo " $label ";
            $length += strlen($label) + 2;

            echo str_repeat('=', $this->maxLength - $length);

        } else {
            $this->printSeparator();
        }
    }

    /**
     * @return void
     */
    protected function printSeparator()
    {
        echo str_repeat('=', $this->maxLength);
    }
}
