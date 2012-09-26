<?php

namespace PHPSpec2;

use PHPSpec2\Prophet\ProphetInterface;
use PHPSpec2\Wrapper\SubjectWrapperInterface;

class ObjectBehavior implements SpecificationInterface, SubjectWrapperInterface
{
    protected $object;

    public function setProphet(ProphetInterface $prophet)
    {
        $this->object = $prophet;
    }

    public function getWrappedSubject()
    {
        return $this->object->getWrappedSubject();
    }

    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(array($this->object, $method), $arguments);
    }

    public function __set($property, $value)
    {
        $this->object->$property = $value;
    }

    public function __get($property)
    {
        return $this->object->$property;
    }
}
