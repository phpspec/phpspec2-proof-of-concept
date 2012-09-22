<?php

namespace PHPSpec2;

interface SpecificationInterface
{
    public function isAnInstanceOf($class, array $constructorArguments = array());
    public function instantiatedWith();
}
