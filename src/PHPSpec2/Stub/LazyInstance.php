<?php

namespace PHPSpec2\Stub;

use ReflectionClass;

use PHPSpec2\Exception\Stub\StubException;
use PHPSpec2\Exception\Stub\ClassDoesNotExistsException;

class LazyInstance implements LazySubjectInterface
{
    private $classname;
    private $arguments;
    private $instance;

    public function __construct($classname, array $arguments = array())
    {
        $this->classname = $classname;
        $this->arguments = $arguments;
    }

    public function setConstructorArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function getInstance()
    {
        if ($this->instance) {
            return $this->instance;
        }

        if (!is_string($this->classname)) {
            throw new StubException(sprintf(
                'Instantiator expects class name, "%s" got', gettype($this->classname)
            ));
        }

        if (!class_exists($this->classname)) {
            throw new ClassDoesNotExistsException($this->classname);
        }

        $reflection = new ReflectionClass($this->classname);

        return $this->instance = $reflection->newInstanceArgs($this->arguments);
    }
}
