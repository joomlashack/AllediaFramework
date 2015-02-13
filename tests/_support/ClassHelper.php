<?php
namespace Codeception\Module;

use PHPUnit_Framework_Assert;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class ClassHelper extends \Codeception\Module
{
    /**
     * Allow to get a private or protected attribute from any instance
     *
     * @param  mixed  $instance  The instance
     * @param  string $attribute The attribute name
     * @return mixed             The attribute value
     */
    public function getAttributeFromInstance($instance, $attribute)
    {
        return PHPUnit_Framework_Assert::readAttribute($instance, $attribute);
    }

    public function callMethodFromInstance($instance, $method, $args = array())
    {
        $reflectionOfUser = new ReflectionClass(get_class($instance));
        $method = $reflectionOfUser->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($instance, $args);
    }
}
