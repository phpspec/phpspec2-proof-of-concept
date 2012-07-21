<?php

namespace PHPSpec2\Matcher;

abstract class BasicMatcher implements MatcherInterface
{
    final public function positiveMatch($subject, array $parameters)
    {
        if (!$this->matches($subject, $parameters)) {
            throw $this->getFailureException($subject, $parameters);
        }

        return $subject;
    }

    final public function negativeMatch($subject, array $parameters)
    {
        if ($this->matches($subject, $parameters)) {
            throw $this->getNegativeFailureException($subject, $parameters);
        }

        return $subject;
    }

    abstract protected function matches($subject, array $parameters);
    abstract protected function getFailureException($subject, array $parameters);
    abstract protected function getNegativeFailureException($subject, array $parameters);
}
