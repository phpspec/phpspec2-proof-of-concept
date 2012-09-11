<?php

namespace PHPSpec2\Prophet;

use ReflectionClass;

use PHPSpec2\Mocker\MockerFactory;

use PHPSpec2\Exception\Prophet\ProphetException;
use PHPSpec2\Exception\Prophet\ClassDoesNotExistsException;

class LazyMock implements LazySubjectInterface
{
    private $classOrInterface;
    private $mockers;
    private $instance;

    public function __construct($classOrInterface, MockerFactory $mockers)
    {
        $this->classOrInterface = $classOrInterface;
        $this->mockers          = $mockers;
    }

    public function getInstance()
    {
        if ($this->instance) {
            return $this->instance;
        }

        if (!is_string($this->classOrInterface)) {
            throw new ProphetException(sprintf(
                'Mock creator expects class or interface name, "%s" got',
                gettype($this->classOrInterface)
            ));
        }

        if (!class_exists($this->classOrInterface) && !interface_exists($this->classOrInterface)) {
            throw new ClassDoesNotExistsException($this->classOrInterface);
        }

        return $this->instance = $this->mockers->mock($this->classOrInterface);
    }
}
