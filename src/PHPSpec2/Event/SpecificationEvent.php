<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use PHPSpec2\Loader\Node\Specification;

class SpecificationEvent extends Event implements EventInterface
{
    private $specification;
    private $time;
    private $result;

    public function __construct(Specification $specification, $time = null, $result = null)
    {
        $this->specification = $specification;
        $this->time          = $time;
        $this->result        = null;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getResult()
    {
        return $this->result;
    }
}
