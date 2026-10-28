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
use Tests\Support\Fixtures\LegacyInflector;

/**
 * Runs against the Doctrine inflector backend, which is what Joomla 4+ ships
 */
class InflectorTest extends Unit
{
    protected Inflector $inflector;

    protected function setUp(): void
    {
        parent::setUp();

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

    #[DataProvider('wordProvider')]
    public function testJoomlaMethodNames(string $singular, string $plural)
    {
        $this->assertSame($plural, $this->inflector->toPlural($singular));
        $this->assertSame($singular, $this->inflector->toSingular($plural));
    }

    public function testIsCountable()
    {
        $this->assertTrue($this->inflector->isCountable('id'));
        $this->assertTrue($this->inflector->isCountable('hits'));
        $this->assertFalse($this->inflector->isCountable('item'));
    }

    public function testCustomRules()
    {
        $inflector = new Inflector([
            'bonus' => 'bonuses',
            'Ox'    => 'Oxen',
            'news'  => null,
        ]);

        $this->assertSame('bonuses', $inflector->pluralize('bonus'));
        $this->assertSame('bonus', $inflector->singularize('bonuses'));
        $this->assertSame('bonuses', $inflector->pluralize('bonuses'));
        $this->assertSame('bonus', $inflector->singularize('bonus'));
        $this->assertTrue($inflector->isPlural('bonuses'));
        $this->assertFalse($inflector->isPlural('bonus'));
        $this->assertTrue($inflector->isSingular('bonus'));
        $this->assertFalse($inflector->isSingular('bonuses'));

        $this->assertSame('oxen', $inflector->pluralize('ox'));
        $this->assertSame('Oxen', $inflector->pluralize('Ox'));
        $this->assertSame('ox', $inflector->singularize('oxen'));

        $this->assertSame('news', $inflector->pluralize('news'));
        $this->assertSame('news', $inflector->singularize('news'));

        $this->assertSame('items', $inflector->pluralize('item'));
        $this->assertSame('category', $inflector->singularize('categories'));
    }

    public static function uncountableProvider(): array
    {
        return [
            'null'      => [null],
            'empty'     => [''],
            'same word' => ['equipment'],
        ];
    }

    #[DataProvider('uncountableProvider')]
    public function testOnlyUncountableCustomRules(?string $plural)
    {
        $inflector = new Inflector(['equipment' => $plural]);

        $this->assertSame('equipment', $inflector->pluralize('equipment'));
        $this->assertSame('equipment', $inflector->singularize('equipment'));
        $this->assertSame('items', $inflector->pluralize('item'));
    }

    public function testLegacyBackendReturnsWordWhenNoInflection()
    {
        $this->setBackend(new LegacyInflector());

        $this->assertSame('items', $this->inflector->pluralize('item'));
        $this->assertSame('item', $this->inflector->singularize('items'));

        $this->assertSame('items', $this->inflector->pluralize('items'));
        $this->assertSame('item', $this->inflector->singularize('item'));
        $this->assertSame('items', $this->inflector->toPlural('items'));
        $this->assertSame('item', $this->inflector->toSingular('item'));
    }

    public function testLegacyBackendIsCountable()
    {
        $this->setBackend(new LegacyInflector());

        $this->assertTrue($this->inflector->isCountable('legacy'));
        $this->assertFalse($this->inflector->isCountable('id'));
    }

    protected function setBackend(object $backend): void
    {
        $property = new \ReflectionProperty($this->inflector, 'inflector');
        $property->setAccessible(true);
        $property->setValue($this->inflector, $backend);
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
