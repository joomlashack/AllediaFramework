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

use Alledia\Framework\Joomla\Extension\Helper;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

class ExtensionHelperTest extends Unit
{
    public static function fullElementProvider(): array
    {
        return [
            'component, prefixed'   => ['component', 'com_myextension', null, 'com_myextension'],
            'component, bare'       => ['component', 'myextension', null, 'com_myextension'],
            'module, prefixed'      => ['module', 'mod_mymodule', null, 'mod_mymodule'],
            'module, bare'          => ['module', 'mymodule', null, 'mod_mymodule'],
            'plugin'                => ['plugin', 'myplugin', 'system', 'plg_system_myplugin'],
            'library'               => ['library', 'allediaframework', null, 'lib_allediaframework'],
            'unknown type'          => ['widget', 'something', null, null],
            'name starts like com_' => ['component', 'combo', null, 'com_combo'],
            'name starts like mod_' => ['module', 'modals', null, 'mod_modals'],
        ];
    }

    #[DataProvider('fullElementProvider')]
    public function testGetFullElementFromInfo(string $type, string $element, ?string $folder, ?string $expected)
    {
        $this->assertSame($expected, Helper::getFullElementFromInfo($type, $element, $folder));
    }

    public static function extensionInfoProvider(): array
    {
        return [
            'component' => [
                'com_myextension',
                [
                    'prefix'    => 'com',
                    'type'      => 'component',
                    'name'      => 'myextension',
                    'group'     => null,
                    'namespace' => 'myextension',
                ],
            ],
            'plugin'    => [
                'plg_system_myplugin',
                [
                    'prefix'    => 'plg',
                    'type'      => 'plugin',
                    'name'      => 'myplugin',
                    'group'     => 'system',
                    'namespace' => 'myplugin',
                ],
            ],
            'module'    => [
                'mod_mymodule',
                [
                    'prefix'    => 'mod',
                    'type'      => 'module',
                    'name'      => 'mymodule',
                    'group'     => null,
                    'namespace' => 'mymodule',
                ],
            ],
            'OS-prefixed name' => [
                'com_osmap',
                [
                    'prefix'    => 'com',
                    'type'      => 'component',
                    'name'      => 'osmap',
                    'group'     => null,
                    'namespace' => 'OSMap',
                ],
            ],
        ];
    }

    #[DataProvider('extensionInfoProvider')]
    public function testGetExtensionInfoFromElement(string $element, array $expected)
    {
        $this->assertSame($expected, Helper::getExtensionInfoFromElement($element));
    }

    public function testExtensionInfoIsNullWithoutPrefix()
    {
        $this->assertNull(Helper::getExtensionInfoFromElement('noprefix'));
    }

    public function testExtensionInfoIsNullForUnknownPrefix()
    {
        $this->assertNull(Helper::getExtensionInfoFromElement('xyz_something'));
    }
}
