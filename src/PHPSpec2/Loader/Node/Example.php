<?php

namespace PHPSpec2\Loader\Node;

use ReflectionFunctionAbstract;

class Example extends Node
{
    private $title;
    private $function;
    private $pending = false;
    private $preFunctions  = array();
    private $postFunctions = array();

    public function __construct($title, ReflectionFunctionAbstract $function)
    {
        $this->title    = $title;
        $this->function = $function;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setAsPending($pending = true)
    {
        $this->pending = $pending;
    }

    public function isPending()
    {
        return $this->pending;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getSpecification()
    {
        if (null !== $this->getParent() && $this->getParent() instanceof Specification) {
            return $this->getParent();
        }
    }

    public function addPreFunction(ReflectionFunctionAbstract $preFunction)
    {
        $this->preFunctions[] = $preFunction;
    }

    public function getPreFunctions()
    {
        return $this->preFunctions;
    }

    public function addPostFunction(ReflectionFunctionAbstract $postFunction)
    {
        $this->postFunctions[] = $postFunction;
    }

    public function getPostFunctions()
    {
        return $this->postFunctions;
    }
}
