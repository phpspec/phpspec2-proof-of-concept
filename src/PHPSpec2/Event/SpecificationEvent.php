<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use ReflectionClass;

class SpecificationEvent extends Event implements EventInterface
{
    private $spec;
    private $result;

    public function __construct(ReflectionClass $spec, $result = null)
    {
        $this->spec   = $spec;
        $this->result = null;
    }

    public function getSpecification()
    {
        return $this->spec;
    }

    public function getResult()
    {
        return $this->result;
    }
}
