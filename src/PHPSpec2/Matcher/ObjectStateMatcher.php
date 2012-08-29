<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Example\FailureException;

class ObjectStateMatcher implements MatcherInterface
{
    public function supports($name, $subject, array $arguments)
    {
        return is_object($subject)
            && preg_match('/be.+/', $name)
        ;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        preg_match('/be(.+)/', $name, $matches);
        $method = 'is'.$matches[1];

        if (!method_exists($subject, $method)) {
            throw new MethodNotFoundException($subject, $method);
        }

        if (true !== call_user_func(array($subject, $method))) {
            throw new FailureException(
                "Expected {$method} to return true, got false."
            );
        }
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        preg_match('/be(.+)/', $name, $matches);
        $method = 'is'.$matches[1];

        if (!method_exists($subject, $method)) {
            throw new MethodNotFoundException($subject, $method);
        }

        if (false !== call_user_func(array($subject, $method))) {
            throw new FailureException(
                "Expected {$method} to return false, got true."
            );
        }
    }

    public function getPriority()
    {
        return 0;
    }
}
