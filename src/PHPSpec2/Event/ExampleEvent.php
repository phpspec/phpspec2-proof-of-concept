<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use ReflectionMethod;
use Exception;

class ExampleEvent extends Event implements EventInterface
{
    const PASSED    = 0;
    const PENDING   = 1;
    const FAILED    = 2;

    private $example;
    private $result;
    private $exception;

    public function __construct(ReflectionMethod $example, $result = null, Exception $exception = null)
    {
        $this->example   = $example;
        $this->result    = $result;
        $this->exception = $exception;
    }

    public function getSpecification()
    {
        return $this->example->getDeclaringClass();
    }

    public function getExample()
    {
        return $this->example;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getException()
    {
        return $this->exception;
    }
}
