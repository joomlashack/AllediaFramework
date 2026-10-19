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
use Alledia\Framework\Helper;
use Codeception\Test\Unit;

class HelperTest extends Unit
{
    public function testCreateClassAliasesCreatesMissingAlias()
    {
        $alias = __NAMESPACE__ . '\\CreatedAlias';

        Helper::createClassAliases([AliasSource::class => $alias]);

        $this->assertTrue(class_exists($alias));
        $this->assertInstanceOf(AliasSource::class, new $alias());
    }

    public function testCreateClassAliasesLeavesExistingClassAlone()
    {
        Helper::createClassAliases([AliasSource::class => \ArrayObject::class]);

        $this->assertNotInstanceOf(AliasSource::class, new \ArrayObject());
    }

    public function testCallMethodOnStaticMethod()
    {
        $this->assertSame('static:a:b', Helper::callMethod(CallTarget::class, 'staticMethod', ['a', 'b']));
    }

    public function testCallMethodUsesSingletonWhenAvailable()
    {
        $result = Helper::callMethod(SingletonCallTarget::class, 'whoAmI');

        $this->assertSame(spl_object_id(SingletonCallTarget::getInstance()), $result);
    }

    public function testCallMethodInstantiatesClassWithoutSingleton()
    {
        $this->assertSame('instance:x', Helper::callMethod(CallTarget::class, 'instanceMethod', ['x']));
    }

    public function testCallMethodReturnsTrueForMissingMethod()
    {
        $this->assertTrue(Helper::callMethod(CallTarget::class, 'noSuchMethod'));
    }

    public function testErrorToException()
    {
        $exception = Helper::errorToException(E_WARNING, 'Something happened', '/path/file.php', 99);

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertSame('E_WARNING - Something happened', $exception->getMessage());
        $this->assertSame('/path/file.php', $exception->getFile());
        $this->assertSame(99, $exception->getLine());
    }

    public function testErrorToExceptionWithUnknownErrorNumber()
    {
        $exception = Helper::errorToException(3, 'Odd error', '/path/file.php', 1);

        $this->assertSame('3 - Odd error', $exception->getMessage());
    }
}

class AliasSource
{
}

class CallTarget
{
    public static function staticMethod(string $a, string $b): string
    {
        return "static:{$a}:{$b}";
    }

    public function instanceMethod(string $value): string
    {
        return "instance:{$value}";
    }
}

class SingletonCallTarget
{
    protected static ?self $instance = null;

    public static function getInstance(): self
    {
        return static::$instance ?: (static::$instance = new static());
    }

    public function whoAmI(): int
    {
        return spl_object_id($this);
    }
}
