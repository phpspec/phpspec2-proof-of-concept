<?php

namespace PHPSpec2\Matcher;

class ThrowMatcher implements MatcherInterface
{
    public function supports($name, $subject, array $arguments)
    {
        return 'throw' === $name;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        return new Verification\PositiveThrowVerification($subject, $arguments);
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
    }
}
