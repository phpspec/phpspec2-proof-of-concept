<?php

namespace PHPSpec2;

use PHPSpec2\Prophet\ProphetInterface;
use PHPSpec2\Wrapper\SubjectWrapperInterface;

use ArrayAccess;

class ObjectBehavior implements ArrayAccess, SpecificationInterface, SubjectWrapperInterface
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

    public function offsetExists($key)
    {
        return $this->object->offsetExists($key);
    }

    public function offsetGet($key)
    {
        return $this->object->offsetGet($key);
    }

    public function offsetSet($key, $value)
    {
        $this->object->offsetSet($key, $value);
    }

    public function offsetUnset($key)
    {
        return $this->object->offsetUnset($key);
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
