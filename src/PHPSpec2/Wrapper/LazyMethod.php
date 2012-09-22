<?php

namespace PHPSpec2\Wrapper;

use ReflectionClass;

class LazyMethod extends LazyObject
{
    private $method;
    private $arguments;

    public function __construct($classname = null, array $constructorArguments = array(),
                                $method = null, array $methodArguments = array())
    {
        $this->method    = $method;
        $this->arguments = $methodArguments;

        parent::__construct($classname, $constructorArguments);
    }

    public function setMethodName($method)
    {
        $this->method = $method;
    }

    public function setMethodArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function getInstance()
    {
        return call_user_func_array(array(parent::getInstance(), $this->method), $this->arguments);
    }
}
