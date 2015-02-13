<?php
use \UnitTester;
use \Codeception\Util\Stub;


class TextCest
{
    public function _before(UnitTester $I)
    {

    }

    public function _after(UnitTester $I)
    {

    }

    public function getTagsFromTextWithoutTags(UnitTester $I)
    {
        $content = 'This is a text without a tag...';

        $instance = new Alledia\Framework\Content\Text($content);

        $tags = $instance->getTags('dumbtag');

        $I->assertIsEmptyArray($tags);
    }

    public function getTagsFromTextWithTags(UnitTester $I)
    {
        $content = 'This is a text with tags {othertag}content{/othertag}
                {dumbtag}tag1{/dumbtag} and {dumbtag param1="13" param2="14"}another content{/dumbtag}
                dumy text {dumbtag param1="12"}{/dumbtag}';

        $instance = new Alledia\Framework\Content\Text($content);

        $tags = $instance->getTags('dumbtag');

        $I->assertIsNotEmptyArray($tags);
        $I->assertArrayCount(3, $tags);
        $I->assertClassName('Alledia\Framework\Content\Tag', $tags[0]);
        $I->assertClassName('Alledia\Framework\Content\Tag', $tags[1]);
        $I->assertClassName('Alledia\Framework\Content\Tag', $tags[2]);
    }
}
