<?php

namespace PHPSpec2\Prophet;

use ReflectionClass;

use PHPSpec2\Mocker\MockerInterface;

use PHPSpec2\Exception\Prophet\ProphetException;
use PHPSpec2\Exception\Prophet\ClassDoesNotExistsException;

class LazyMock implements LazySubjectInterface
{
    private $classOrInterface;
    private $mocker;
    private $instance;

    public function __construct($classOrInterface, MockerInterface $mocker)
    {
        $this->classOrInterface = $classOrInterface;
        $this->mocker           = $mocker;
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

        return $this->instance = $this->mocker->mock($this->classOrInterface);
    }
}
