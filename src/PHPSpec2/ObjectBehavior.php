<?php

namespace PHPSpec2;

use ArrayAccess;

use PHPSpec2\Prophet\ProphetInterface;
use PHPSpec2\Wrapper\SubjectWrapperInterface;

class ObjectBehavior implements SpecificationInterface, SubjectWrapperInterface, ArrayAccess
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

    public function offsetExists($offset)
    {
        return $this->object->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->object->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->object->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->object->offsetUnset($offset);
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

    public function __invoke()
    {
        return call_user_func_array(array($this->object, '__invoke'), func_get_args());
    }
}
