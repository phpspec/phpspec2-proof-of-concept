<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use PHPSpec2\Loader\Node\Specification;

class SpecificationEvent extends Event implements EventInterface
{
    private $specification;
    private $result;

    public function __construct(Specification $specification, $result = null)
    {
        $this->specification = $specification;
        $this->result        = null;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function getResult()
    {
        return $this->result;
    }
}
