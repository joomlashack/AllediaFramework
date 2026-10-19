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

use Alledia\Framework\Color;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

class ColorTest extends Unit
{
    public static function nameToHexProvider(): array
    {
        return [
            'named color'         => ['red', '#FF0000'],
            'named color, caps'   => ['DarkBlue', '#00008B'],
            'hex passes through'  => ['#abc', '#abc'],
            'unknown name'        => ['notacolor', null],
        ];
    }

    #[DataProvider('nameToHexProvider')]
    public function testNameToHex(string $name, ?string $expected)
    {
        $this->assertSame($expected, Color::nameToHex($name));
    }

    public static function adjustProvider(): array
    {
        return [
            'no change'           => ['#ff8000', 100, false, '#ff8000'],
            'darken by half'      => ['#ff0000', 50, false, '#800000'],
            'short hex expands'   => ['#abc', 100, false, '#aabbcc'],
            'rounds up (ceil)'    => ['#0a0a0a', 52, false, '#060606'],
            'rounds to nearest'   => ['#0a0a0a', 52, true, '#050505'],
            'named color'         => ['red', 50, false, '800000'],
        ];
    }

    #[DataProvider('adjustProvider')]
    public function testAdjust(string $color, float $percent, bool $useRounding, string $expected)
    {
        $this->assertSame($expected, Color::adjust($color, $percent, $useRounding));
    }

    public function testAdjustReturnsInvalidColorUnchanged()
    {
        $this->assertSame('notacolor', Color::adjust('notacolor', 50));
        $this->assertSame('#12345', Color::adjust('#12345', 50));
    }

    public function testAdjustAcceptsHexWithoutHash()
    {
        // Docblock: "Leading hash mark is optional and the result will be returned in the same format"
        $this->assertSame('800000', Color::adjust('ff0000', 50));
    }

    public function testAdjustLightenStaysWithinValidHex()
    {
        $this->assertSame('#ffffff', Color::adjust('#ffffff', 200));
    }

    public static function hexToRgbProvider(): array
    {
        return [
            'full hex with hash' => ['#ff8000', ['r' => 255, 'g' => 128, 'b' => 0]],
            'full hex, no hash'  => ['0080ff', ['r' => 0, 'g' => 128, 'b' => 255]],
            'short hex'          => ['#f80', ['r' => 255, 'g' => 136, 'b' => 0]],
            'wrong length'       => ['#12345', null],
        ];
    }

    #[DataProvider('hexToRgbProvider')]
    public function testHexToRgb(string $hex, ?array $expected)
    {
        $this->assertSame($expected, Color::hexToRgb($hex));
    }

    public function testHexToRgbRejectsNonHexCharacters()
    {
        $this->assertNull(Color::hexToRgb('#ggggg1'));
    }

    public function testHexToRgbReturnsNullForEmptyValue()
    {
        $this->assertNull(Color::hexToRgb(null));
        $this->assertNull(Color::hexToRgb(''));
    }

    public static function brightnessProvider(): array
    {
        return [
            'white'  => ['#ffffff', true],
            'black'  => ['#000000', false],
            'yellow' => ['yellow', true],
            'navy'   => ['navy', false],
        ];
    }

    #[DataProvider('brightnessProvider')]
    public function testIsTooBright(string $color, bool $expected)
    {
        $this->assertSame($expected, Color::isTooBright($color));
    }

    public function testIsTooBrightUsesMidpoint()
    {
        $this->assertTrue(Color::isTooBright('#808080'));
        $this->assertFalse(Color::isTooBright('#808080', 200));
    }

    public function testIsTooBrightIsFalseForInvalidColor()
    {
        $this->assertFalse(Color::isTooBright('notacolor'));
    }
}
