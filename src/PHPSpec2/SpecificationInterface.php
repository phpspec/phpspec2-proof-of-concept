<?php

namespace PHPSpec2;

interface SpecificationInterface
{
    public function objectIsAnInstanceOf($class, array $constructorArguments = array());
    public function objectIsConstructedWith();
}
