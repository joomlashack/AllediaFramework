<?php
use \UnitTester;
use \JRegistry;

require_once __DIR__ . '/../../_support/mock/AutoLoaderMock.php';

class AutoLoaderCest
{
    /**
     * @var AutoLoaderMock
     */
    protected $loader = null;

    /**
     * @var string
     */
    protected $baseMockDir;

    public function _before(UnitTester $I)
    {
        $this->baseMockDir = __DIR__ . '/../../_support/mock/autoloader';

        AutoLoaderMock::setFiles(array(
            $this->baseMockDir . '/vendor/foo.bar/src/ClassName.php',
            $this->baseMockDir . '/vendor/foo.bar/src/DoomClassName.php',
            $this->baseMockDir . '/vendor/foo.bar/tests/ClassNameTest.php',
            $this->baseMockDir . '/vendor/foo.bardoom/src/ClassName.php',
            $this->baseMockDir . '/vendor/foo.bar.baz.dib/src/ClassName.php',
            $this->baseMockDir . '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php',
            $this->baseMockDir . '/local/camels/class.php',
            $this->baseMockDir . '/local/camels/foo/bar.php',
            $this->baseMockDir . '/local/camels/foobar.php',
            $this->baseMockDir . '/local2/camels/class.php',
            $this->baseMockDir . '/local2/camels/foo/bar.php'
        ));

        $this->loader = AutoLoaderMock::getInstance();

        AutoLoaderMock::register('Foo\Bar', $this->baseMockDir . '/vendor/foo.bar/src');
        AutoLoaderMock::register('Foo\Bar', $this->baseMockDir . '/vendor/foo.bar/tests');
        AutoLoaderMock::register('Foo\BarDoom', $this->baseMockDir . '/vendor/foo.bardoom/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib', $this->baseMockDir . '/vendor/foo.bar.baz.dib/src');
        AutoLoaderMock::register('Foo\Bar\Baz\Dib\Zim\Gir', $this->baseMockDir . '/vendor/foo.bar.baz.dib.zim.gir/src');

        AutoLoaderMock::registerCamelBase('Camel', $this->baseMockDir . '/local/camels');
        AutoLoaderMock::registerCamelBase('Hump', $this->baseMockDir . '/local2/camels');
    }

    public function _after(UnitTester $I)
    {
    }

    public function testClassExisting(UnitTester $I)
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassName');
        $expect = $this->baseMockDir . '/vendor/foo.bar/src/ClassName.php';
        $I->assertEquals ($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\Bar\ClassNameTest');
        $expect = $this->baseMockDir . '/vendor/foo.bar/tests/ClassNameTest.php';
        $I->assertEquals($expect, $actual);
    }

    public function testClassMissing(UnitTester $I)
    {
        $actual = $this->loader->mockLoadClass('No_Vendor\No_Package\NoClass');
        $I->assertFalse($actual);
    }

    public function testClassDeep(UnitTester $I)
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');
        $expect = $this->baseMockDir . '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
        $I->assertEquals($expect, $actual);
    }

    public function testClassConfusion(UnitTester $I)
    {
        $actual = $this->loader->mockLoadClass('Foo\Bar\DoomClassName');
        $expect = $this->baseMockDir . '/vendor/foo.bar/src/DoomClassName.php';
        $I->assertEquals($expect, $actual);

        $actual = $this->loader->mockLoadClass('Foo\BarDoom\ClassName');
        $expect = $this->baseMockDir . '/vendor/foo.bardoom/src/ClassName.php';
        $I->assertEquals($expect, $actual);
    }

    public function testCamelExisting(UnitTester $I)
    {
        $actual = $this->loader->mockLoadCamelClass('CamelClass');
        $expect = $this->baseMockDir . '/local/camels/class.php';
        $I->assertEquals($expect, $actual);
    }

    public function testCamelMissing(UnitTester $I)
    {
        $actual = $this->loader->mockLoadCamelClass('NoSuchClass');
        $I->assertFalse($actual);
    }

    public function testCamelDeep(UnitTester $I)
    {
        $actual = $this->loader->mockLoadCamelClass('CamelFooBar');
        $expect = $this->baseMockDir . '/local/camels/foo/bar.php';
        $I->assertEquals($expect, $actual);
    }

    // @TODO: Review this test
    public function testCamelConfusion(UnitTester $I)
    {
        $actual = $this->loader->mockLoadCamelClass('CamelFooBar');
        $expect = $this->baseMockDir . '/local/camels/foo/bar.php';
        $I->assertEquals($expect, $actual);

        // $actual = $this->loader->mockLoadCamelClass('CamelFoobar');
        // $expect = $this->baseMockDir . '/local/camels/foobar.php';
        // $I->assertEquals($expect, $actual);

        // $actual = $this->loader->mockLoadCamelClass('HumpFooBar');
        // $expect = $this->baseMockDir . '/local2/camels/foo/bar.php';
        // $I->assertEquals($expect, $actual);
    }
}
