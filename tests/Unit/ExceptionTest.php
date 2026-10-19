<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2026 Joomlashack.com. All rights reserved
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

namespace Tests\Unit;

use Alledia\Framework\Exception;
use Alledia\Framework\Exception\NotFound;
use Codeception\Test\Unit;

class ExceptionTest extends Unit
{
    public function testSetLocation()
    {
        $exception = new Exception('message');
        $exception->setLocation('/some/file.php', 42);

        $this->assertSame('/some/file.php', $exception->getFile());
        $this->assertSame(42, $exception->getLine());
    }

    public function testTraceMessageIncludesCaller()
    {
        $exception = $this->createException('Something failed');

        $this->assertSame(static::class . '::createException() Something failed', $exception->getTraceMessage());
    }

    public function testCallStack()
    {
        $stack = $this->createException('x')->getCallStack();

        $this->assertNotEmpty($stack);
        $this->assertStringStartsWith('Line ', $stack[0]);
        $this->assertStringContainsString(static::class . '::createException()', $stack[0]);
    }

    public function testNotFoundIsFrameworkException()
    {
        $this->assertInstanceOf(Exception::class, new NotFound());
    }

    protected function createException(string $message): Exception
    {
        return new Exception($message);
    }
}
