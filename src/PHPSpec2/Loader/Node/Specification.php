<?php

namespace PHPSpec2\Loader\Node;

use ReflectionClass;

class Specification extends Node
{
    private $title;
    private $class;
    private $children = array();

    public function __construct($title, ReflectionClass $class = null)
    {
        $this->title = $title;
        $this->class = $class;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setClass(ReflectionClass $class)
    {
        return $this->class;
    }

    public function getClass()
    {
        return $this->class;
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
