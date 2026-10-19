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

use Alledia\Framework\Base;
use Alledia\Framework\Exception;
use Codeception\Test\Unit;

class BaseTest extends Unit
{
    public function testGetPropertiesReturnsPublicOnlyByDefault()
    {
        $object = new BaseFixture();

        $this->assertSame(['id' => null, 'title' => null], $object->getProperties());
    }

    public function testGetPropertiesCanIncludeProtected()
    {
        $object = new BaseFixture();

        $this->assertSame(
            ['id' => null, 'title' => null, 'secret' => 'protected'],
            $object->getProperties(false)
        );
    }

    public function testSetPropertiesFromArraySetsOnlyPublicProperties()
    {
        $object = (new BaseFixture())->setProperties([
            'id'      => 5,
            'title'   => 'Title',
            'secret'  => 'changed',
            'unknown' => 'ignored',
        ]);

        $this->assertSame(['id' => 5, 'title' => 'Title', 'secret' => 'protected'], $object->getProperties(false));
    }

    public function testSetPropertiesFromObject()
    {
        $object = (new BaseFixture())->setProperties((object)['id' => 7, 'title' => 'From object']);

        $this->assertSame(7, $object->id);
        $this->assertSame('From object', $object->title);
    }

    public function testSetPropertiesUsingMap()
    {
        $object = (new BaseFixture())->setProperties(['id' => 3, 'name' => 'Mapped'], ['title' => 'name']);

        $this->assertSame(3, $object->id);
        $this->assertSame('Mapped', $object->title);
    }

    public function testSetPropertiesRejectsScalar()
    {
        $this->expectException(Exception::class);

        (new BaseFixture())->setProperties('not valid');
    }

    public function testMapTranslatesFieldValues()
    {
        $map = [
            'status' => [
                'state' => [
                    'active'            => 1,
                    'closed'            => 0,
                    Base::MAP_UNDEFINED => -1,
                ],
            ],
        ];

        $object = new BaseFixture();

        $this->assertSame(['status' => 1], $object->map(['state' => 'active'], ['status'], $map));
        $this->assertSame(['status' => 0], $object->map(['state' => 'closed'], ['status'], $map));
        $this->assertSame(['status' => -1], $object->map(['state' => 'other'], ['status'], $map));
    }

    public function testMapFillsMissingKeysWithNull()
    {
        $object = new BaseFixture();

        $this->assertSame(['id' => 1, 'title' => null], $object->map((object)['id' => 1], ['id', 'title']));
    }

    public function testMapRejectsScalarSource()
    {
        $this->expectException(Exception::class);

        (new BaseFixture())->map('not valid', ['id']);
    }

    public function testClearProperties()
    {
        $object = (new BaseFixture())->setProperties(['id' => 1, 'title' => 'x']);

        $object->clearProperties();
        $this->assertSame(['id' => null, 'title' => null, 'secret' => 'protected'], $object->getProperties(false));

        $object->clearProperties(false);
        $this->assertSame(['id' => null, 'title' => null, 'secret' => null], $object->getProperties(false));
    }

    public function testGetKeyValue()
    {
        $object = new BaseFixture();

        $this->assertSame('a', $object->getKeyValue(['key' => 'a'], 'key'));
        $this->assertSame('b', $object->getKeyValue((object)['key' => 'b'], 'key'));
        $this->assertSame('default', $object->getKeyValue([], 'key', 'default'));
    }

    public function testMagicGetUsesGetter()
    {
        $object = new BaseFixture();

        $this->assertSame('computed value', $object->computed);
        $this->assertNull($object->nothing);
    }

    public function testStringConversionReturnsClassName()
    {
        $this->assertSame(BaseFixture::class, (string)new BaseFixture());
    }
}

class BaseFixture extends Base
{
    public $id;

    public $title;

    protected $secret = 'protected';

    public function getComputed(): string
    {
        return 'computed value';
    }
}
