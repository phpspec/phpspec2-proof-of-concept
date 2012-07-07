<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub;

interface MatcherInterface
{
    public function getAliases();
    public function match(Stub $stub, array $arguments);
}
