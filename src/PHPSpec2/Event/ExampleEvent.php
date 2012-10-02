<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use PHPSpec2\Loader\Node\Example;

class ExampleEvent extends Event implements EventInterface
{
    const PASSED  = 0;
    const PENDING = 1;
    const FAILED  = 2;

    private $example;
    private $time;
    private $result;
    private $exception;

    public function __construct(Example $example, $time = null, $result = null,
                                \Exception $exception = null)
    {
        $this->example   = $example;
        $this->time      = $time;
        $this->result    = $result;
        $this->exception = $exception;
    }

    public function getExample()
    {
        return $this->example;
    }

    public function getSpecification()
    {
        return $this->example->getSpecification();
    }

    public function getTime()
    {
        return $this->time;
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
