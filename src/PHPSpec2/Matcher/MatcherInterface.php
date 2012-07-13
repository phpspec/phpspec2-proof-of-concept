<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub\ObjectStub;

interface MatcherInterface
{
    public function getAliases();
    public function match(ObjectStub $stub, $alias, array $arguments);
}
