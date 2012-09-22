<?php

namespace PHPSpec2\Wrapper;

use ReflectionClass;

use PHPSpec2\Exception\Prophet\ProphetException;
use PHPSpec2\Exception\Prophet\ClassDoesNotExistsException;

class LazyObject implements LazySubjectInterface
{
    private $classname;
    private $arguments;
    private $instance;

    public function __construct($classname = null, array $arguments = array())
    {
        $this->classname = $classname;
        $this->arguments = $arguments;
    }

    public function setClassname($classname)
    {
        $this->classname = $classname;
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

        if (null === $this->classname || !is_string($this->classname)) {
            throw new ProphetException(sprintf(
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
