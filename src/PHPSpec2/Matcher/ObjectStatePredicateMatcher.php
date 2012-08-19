<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class ObjectStatePredicateMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return 0 === strpos($name, 'be_')
            && is_object($subject);
    }

    public function matches($name, $subject, array $arguments)
    {
        $method = 'is' . substr(str_replace('_', '', $name), 2);
        return call_user_func(array($subject, $method)) === true;
    }

    public function getFailureException($name, $subject, array $arguments)
    {
        $method = preg_replace(
            array('#(_)([A-Za-z]{1})#e','#(^[A-Za-z]{1})#e'),
            array("strtoupper('\\2')","strtoupper('\\1')"),
            $name
        );
        $method = 'is' . substr(str_replace('_', '', $method), 2);
        return new FailureException(
            "Expected {$method} to return true, got false. Using ($name)"
        );
    }

    public function getNegativeFailureException($name, $subject, array $arguments)
    {
        $method = preg_replace(
            array('#(_)([A-Za-z]{1})#e','#(^[A-Za-z]{1})#e'),
            array("strtoupper('\\2')","strtoupper('\\1')"),
            $name
        );
        $method = 'is' . substr(str_replace('_', '', $method), 2);
        return new FailureException(
            "Expected $method not to return true, got true. Using ($name)"
        );
    }
}