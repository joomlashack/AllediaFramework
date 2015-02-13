<?php
use \UnitTester;
use \Codeception\Util\Stub;


class TagCest
{
    public function _before(UnitTester $I)
    {

    }

    public function _after(UnitTester $I)
    {

    }

    public function getRegexForTag(UnitTester $I)
    {
        $regex = Alledia\Framework\Content\Tag::getRegex('dumbtag');

        $I->assertIsString($regex);
        $I->assertGreaterThan(0, strlen($regex));
    }

    public function getUnparsedContentFromTag(UnitTester $I)
    {
        $tag = '{dumbtag param1="32"}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $I->assertEquals($tag, $instance->toString());
    }

    public function getParsedContentFromTagWithContent(UnitTester $I)
    {
        $tag = '{dumbtag param1="32"}a dumb content{/dumbtag}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $I->assertEquals('a dumb content', $instance->getContent());
    }

    public function getEmptyParsedContentFromTagWithoutContent(UnitTester $I)
    {
        $tag = '{dumbtag param1="32"}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $I->assertEmpty($instance->getContent());
    }

    public function getTagName(UnitTester $I)
    {
        $tag = '{dumbtag param1="32"}a dumb content{/dumbtag}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $I->assertEquals('dumbtag', $instance->getName());
    }

    public function getParamsFromTagWithParams(UnitTester $I)
    {
        $tag = '{dumbtag param1="32" param2="992"}a dumb content{/dumbtag}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $expected = new \JRegistry(array(
            'param1' => "32",
            'param2' => "992"
        ));

        $params = $instance->getParams();

        $I->assertClassName('Joomla\Registry\Registry', $params);
        $I->assertEqualsSerializing($expected, $params);
    }

    public function getParamsFromTagWithoutParams(UnitTester $I)
    {
        $tag = '{dumbtag}a dumb content{/dumbtag}';

        $instance = new Alledia\Framework\Content\Tag('dumbtag', $tag);

        $expected = new \JRegistry;

        $params = $instance->getParams();

        $I->assertClassName('Joomla\Registry\Registry', $params);
        $I->assertEqualsSerializing($expected, $params);
    }
}
