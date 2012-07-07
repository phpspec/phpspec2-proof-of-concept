<?php

namespace PHPSpec\PHPSpec2\Matcher;

use PHPSpec\PHPSpec2\Stub;

interface MatcherInterface
{
    public function getAliases();
    public function match(Stub $stub, array $arguments);
}
