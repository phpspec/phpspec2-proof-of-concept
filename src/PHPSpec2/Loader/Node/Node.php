<?php

namespace PHPSpec2\Loader\Node;

abstract class Node
{
    private $subject;
    private $parent;

    public function __construct($subject = null)
    {
        $this->subject = $subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        if (null !== $this->subject) {
            return $this->subject;
        }

        if (null !== $this->parent) {
            return $this->parent->getSubject();
        }
    }

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
