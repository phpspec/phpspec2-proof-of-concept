<?php

namespace PHPSpec2\Loader\Node;

class Specification extends Node
{
    private $title;
    private $subject;
    private $children = array();

    public function __construct($title, $subject = null)
    {
        $this->title   = $title;
        $this->subject = $subject;
    }

    public function getTitle()
    {
        return $this->title;
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

        if (null !== $parent = $this->getParent()) {
            return $parent->getSubject();
        }
    }

    public function addChild(Node $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function getChildren()
    {
        return $this->children;
    }
}
