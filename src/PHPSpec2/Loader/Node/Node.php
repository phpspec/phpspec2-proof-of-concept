<?php

namespace PHPSpec2\Loader\Node;

abstract class Node
{
    private $parent;

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Node $parent)
    {
        $this->parent = $parent;
    }

    public function getDepth()
    {
        return null !== $this->getParent()
             ? $this->getParent()->getDepth() + 1
             : 0
        ;
    }
}
