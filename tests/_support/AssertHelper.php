<?php
namespace Codeception\Module;

use PHPUnit_Framework_Assert;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AssertHelper extends \Codeception\Module
{
    public function assertClassName($expected, $current)
    {
        return get_class($current) === $expected;
    }

    public function assertEqualsSerializing($expected, $current)
    {
        return md5(serialize($expected)) === md5(serialize($current));
    }

    public function assertNotEqualsSerializing($expected, $current)
    {
        return md5(serialize($expected)) !== md5(serialize($current));
    }

    public function assertIsObject($object)
    {
        return is_object($object);
    }

    public function assertIsArray($variable)
    {
        return is_array($variable);
    }
}
