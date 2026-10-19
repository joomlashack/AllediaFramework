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

use Alledia\Framework\Content\Tag;
use Alledia\Framework\Content\Text;
use Codeception\Test\Unit;
use Joomla\Registry\Registry;

class ContentTest extends Unit
{
    public function testFindsAllPluginTags()
    {
        $text = new Text('Before {mytag id="12" style="big"}first{/mytag} middle {mytag}second{/mytag} after');

        $tags = $text->getPluginTags('mytag');

        $this->assertCount(2, $tags);
        $this->assertContainsOnlyInstancesOf(Tag::class, $tags);

        $this->assertSame('first', $tags[0]->getContent());
        $this->assertSame('second', $tags[1]->getContent());
    }

    public function testParsesTagParams()
    {
        [$tag] = (new Text('{mytag id="12" style="big"}content{/mytag}'))->getPluginTags('mytag');

        $this->assertInstanceOf(Registry::class, $tag->getParams());
        $this->assertSame('12', $tag->getParams()->get('id'));
        $this->assertSame('big', $tag->getParams()->get('style'));
    }

    public function testParamsOnlyContainTagAttributes()
    {
        [$tag] = (new Text('{mytag id="12"}Hello World!{/mytag}'))->getPluginTags('mytag');

        $this->assertSame(['id' => '12'], $tag->getParams()->toArray());
    }

    public function testTrimsTagContent()
    {
        [$tag] = (new Text("{mytag}  \n padded \n {/mytag}"))->getPluginTags('mytag');

        $this->assertSame('padded', $tag->getContent());
    }

    public function testIgnoresOtherTags()
    {
        $text = new Text('{other}x{/other} {mytag}y{/mytag}');

        $this->assertCount(1, $text->getPluginTags('mytag'));
        $this->assertSame([], $text->getPluginTags('missing'));
    }

    public function testTagAccessors()
    {
        $tag = new Tag('mytag', '{mytag a="1"}body{/mytag}');

        $this->assertSame('mytag', $tag->getName());
        $this->assertSame('{mytag a="1"}body{/mytag}', $tag->toString());
        $this->assertSame('body', $tag->content);
    }

    public function testDeprecatedGetTagsMatchesGetPluginTags()
    {
        $text = new Text('{mytag}one{/mytag}{mytag}two{/mytag}');

        $this->assertEquals($text->getPluginTags('mytag'), $text->getTags('mytag'));
    }
}
