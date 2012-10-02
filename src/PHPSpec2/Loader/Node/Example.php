<?php

namespace PHPSpec2\Loader\Node;

use ReflectionFunctionAbstract;

class Example extends Node
{
    private $title;
    private $subject;
    private $function;
    private $preFunctions  = array();
    private $postFunctions = array();

    public function __construct($title, $subject, ReflectionFunctionAbstract $function)
    {
        $this->title    = $title;
        $this->subject  = $subject;
        $this->function = $function;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSubject()
    {
        return $this->subject;
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
