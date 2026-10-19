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

use Alledia\Framework\Joomla\String\Inflector;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

/**
 * Runs against the Doctrine inflector backend, which is what Joomla 4+ ships
 */
class InflectorTest extends Unit
{
    protected Inflector $inflector;

    protected function _before()
    {
        $this->inflector = new Inflector();
    }

    public static function wordProvider(): array
    {
        return [
            ['item', 'items'],
            ['category', 'categories'],
            ['person', 'people'],
            ['address', 'addresses'],
        ];
    }

    #[DataProvider('wordProvider')]
    public function testPluralizeAndSingularize(string $singular, string $plural)
    {
        $this->assertSame($plural, $this->inflector->pluralize($singular));
        $this->assertSame($singular, $this->inflector->singularize($plural));
    }

    #[DataProvider('wordProvider')]
    public function testIsPlural(string $singular, string $plural)
    {
        $this->assertTrue($this->inflector->isPlural($plural), "'{$plural}' should be plural");
        $this->assertFalse($this->inflector->isPlural($singular), "'{$singular}' should not be plural");
    }

    #[DataProvider('wordProvider')]
    public function testIsSingular(string $singular, string $plural)
    {
        $this->assertTrue($this->inflector->isSingular($singular), "'{$singular}' should be singular");
        $this->assertFalse($this->inflector->isSingular($plural), "'{$plural}' should not be singular");
    }

    public function testPassesThroughBackendMethods()
    {
        $this->assertSame('model_name', $this->inflector->tableize('ModelName'));
    }

    public function testUnknownMethodThrows()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Call to undefined method ' . Inflector::class . '::noSuchMethod()');

        $this->inflector->noSuchMethod();
    }
}
