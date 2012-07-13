<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub\ObjectStub;

abstract class BipolarMatcher implements MatcherInterface
{
    abstract public function getPositiveAliases();
    abstract public function getNegativeAliases();
    abstract public function positiveMatch(ObjectStub $stub, array $arguments);
    abstract public function negativeMatch(ObjectStub $stub, array $arguments);

    final public function getAliases()
    {
        return array_merge($this->getPositiveAliases(), $this->getNegativeAliases());
    }

    final public function match(ObjectStub $stub, $alias, array $arguments)
    {
        if (in_array($alias, $this->getPositiveAliases())) {
            return $this->positiveMatch($stub, $arguments);
        } else {
            return $this->negativeMatch($stub, $arguments);
        }
    }
}
