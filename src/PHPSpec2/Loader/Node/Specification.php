<?php

namespace PHPSpec2\Loader\Node;

class Specification extends Node
{
    private $title;
    private $children = array();

    public function __construct($title, $subject = null)
    {
        $this->title = $title;
        parent::__construct($subject);
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
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
