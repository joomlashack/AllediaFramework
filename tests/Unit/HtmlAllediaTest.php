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

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use JhtmlAlledia;

class HtmlAllediaTest extends Unit
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once ALLEDIA_FRAMEWORK_SRC . '/helpers/html/alledia.php';
    }

    public static function truncateProvider(): array
    {
        return [
            'shorter than limit'   => ['Short text', 20, 'Short text'],
            'exactly at limit'     => ['Exactly twenty chars', 20, 'Exactly twenty chars'],
            'breaks on space'      => ['The quick brown fox jumps', 15, 'The quick...'],
            'breaks on hyphen'     => ['well-known-fact-here', 14, 'well-known...'],
            'breaks on underscore' => ['snake_case_words_here', 15, 'snake_case...'],
            'multibyte text'       => ['Ünïcödé wörds hëre', 12, 'Ünïcödé...'],
        ];
    }

    #[DataProvider('truncateProvider')]
    public function testTruncate(string $string, int $limit, string $expected)
    {
        $this->assertSame($expected, JhtmlAlledia::truncate($string, $limit));
    }

    public function testTruncateWithCustomContinuation()
    {
        $this->assertSame('The quick brown [more]', JhtmlAlledia::truncate('The quick brown fox jumps', 22, ' [more]'));
    }

    public function testTruncatedResultNeverExceedsLimit()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';

        foreach (range(10, mb_strlen($string)) as $limit) {
            $this->assertLessThanOrEqual($limit, mb_strlen(JhtmlAlledia::truncate($string, $limit)), "Limit {$limit}");
        }
    }
}
