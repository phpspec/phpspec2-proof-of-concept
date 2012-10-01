<?php

namespace PHPSpec2\Subject;

use PHPSpec2\Exception\MethodNotFoundException;

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
        $instance = parent::getInstance();

        if (!method_exists($instance, $this->method) && !method_exists($instance, '__call')) {
            throw new MethodNotFoundException($instance, $this->method);
        }

        return call_user_func_array(array($instance, $this->method), $this->arguments);
    }
}
