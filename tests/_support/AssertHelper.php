<?php
namespace Codeception\Module;

use PHPUnit_Framework_Assert;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AssertHelper extends \Codeception\Module
{
    private function assertResult($result)
    {
        $result = (bool) $result;

        if (!$result) {
            $this->fail(null);
        }

        return $result;
    }

    public function assertClassName($expected, $current)
    {
        $result = get_class($current) === $expected;

        return $this->assertResult($result);
    }

    public function assertEqualsSerializing($expected, $current)
    {
        $result = md5(serialize($expected)) === md5(serialize($current));

        return $this->assertResult($result);
    }

    public function assertNotEqualsSerializing($expected, $current)
    {
        $result = md5(serialize($expected)) !== md5(serialize($current));

        return $this->assertResult($result);
    }

    public function assertIsObject($object)
    {
        $result = is_object($object);

        return $this->assertResult($result);
    }

    public function assertIsArray($variable)
    {
        $result = is_array($variable);

        return $this->assertResult($result);
    }

    public function assertIsEmptyArray($variable)
    {
        $result = assertIsArray($variable) && (count($variable) === 0);

        return $this->assertResult($result);
    }

    public function assertIsString($variable)
    {
        $result = is_string($variable);

        return $this->assertResult($result);
    }
}
