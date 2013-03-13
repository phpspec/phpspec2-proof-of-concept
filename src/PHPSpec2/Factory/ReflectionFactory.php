<?php

namespace PHPSpec2\Factory;

class ReflectionFactory
{
    public function create($class)
    {
        return new \ReflectionClass($class);
    }
}

