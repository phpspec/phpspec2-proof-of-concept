<?php

namespace PHPSpec2\Stub;

use ReflectionClass;

class LazySubject
{
    private $reflection;
    private $arguments;

    public function __construct(ReflectionClass $reflection, array $arguments = array())
    {
        $this->reflection = $reflection;
        $this->arguments  = $arguments;
    }

    public function setConstructorArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function instantiate()
    {
        return $this->reflection->newInstanceArgs($this->arguments);
    }
}
