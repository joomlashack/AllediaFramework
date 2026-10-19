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

use Alledia\Framework\AutoLoader;
use Codeception\Test\Unit;

/**
 * AutoLoader state is static and classes can only load once,
 * so every test uses its own namespace prefix.
 */
class AutoLoaderTest extends Unit
{
    protected string $dataPath;

    protected function _before()
    {
        $this->dataPath = codecept_data_dir('AutoLoader');
    }

    public function testFrameworkIsAutoloaded()
    {
        $this->assertTrue(class_exists(\Alledia\Framework\Base::class));
    }

    public function testLoadsClassFromRegisteredPrefix()
    {
        AutoLoader::register('AfTest\\Psr4', $this->dataPath . '/Psr4First');

        $this->assertTrue(class_exists(\AfTest\Psr4\Widget::class));
        $this->assertTrue(class_exists(\AfTest\Psr4\Sub\Gadget::class));
    }

    public function testSearchesAllBaseDirectoriesForPrefix()
    {
        AutoLoader::register('AfTest\\Psr4', $this->dataPath . '/Psr4First');
        AutoLoader::register('AfTest\\Psr4', $this->dataPath . '/Psr4Second');

        $this->assertTrue(class_exists(\AfTest\Psr4\OnlySecond::class));
    }

    public function testPrependedDirectoryIsSearchedFirst()
    {
        AutoLoader::register('\\AfTest\\Prepend\\', $this->dataPath . '/PrependA');
        AutoLoader::register('AfTest\\Prepend', $this->dataPath . '/PrependB/', true);

        $this->assertSame('B', \AfTest\Prepend\Which::SOURCE);
    }

    public function testIgnoresMissingBaseDirectory()
    {
        AutoLoader::register('AfTest\\Missing', $this->dataPath . '/DoesNotExist');

        $this->assertFalse(class_exists('AfTest\\Missing\\Anything'));
    }

    public function testUnregisteredClassIsNotLoaded()
    {
        $this->assertFalse(class_exists('AfTest\\Psr4\\NoSuchClass'));
    }

    public function testLoadsCamelCaseClass()
    {
        AutoLoader::registerCamelBase('AfTestCamel', $this->dataPath . '/Camel');

        $this->assertTrue(class_exists('AfTestCamelViewAdmin'));
        $this->assertFalse(class_exists('AfTestCamelViewMissing'));
    }
}
