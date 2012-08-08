<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;
use PHPSpec2\Exception\Example\FailureException;

class PredicateMatcher extends BasicMatcher
{
    private $name;

    public function supports($name, $subject, array $arguments)
    {
        $this->name = $name;
        return is_object($subject) && $this->isPredicate($subject);
    }

    protected function matches($subject, array $arguments)
    {
        if (!$this->name) {
            throw new ExampleException(
                "Before using PredicateMatcher, check if it supports the alias"
            );
        }

        if ($method = $this->detectPredicateMethod('be', 'is', $subject)) {
            return call_user_func(array($subject, $method)) === true;
        } elseif ($method = $this->detectPredicateMethod('have', 'has', $subject)) {
            return call_user_func_array(array($subject, $method), $arguments);
        }
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        if ($method = $this->detectPredicateMethod('be', 'is', $subject)) {
            $method = strtoupper($method[0]) . substr($method, 1);
            return new FailureException(
                "Expected $method to return true, got false. Using ($name)"
            );
        } elseif ($method = $this->detectPredicateMethod('have', 'has', $subject)) {
            return new FailureException(
                "Expected $method to return true, got false. Using ($name)"
            );
        }
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        if ($method = $this->detectPredicateMethod('be', 'is', $subject)) {
            $method = strtoupper($method[0]) . substr($method, 1);
            return new FailureException(
                "Expected $method not to return true, got true. Using ($name)"
            );
        } elseif ($method = $this->detectPredicateMethod('have', 'has', $subject)) {
            return new FailureException(
                "Expected $method not to return true, got true. Using ($name)"
            );
        }
    }

    private function isPredicate($subject)
    {
        if ($this->detectPredicateMethod('be', 'is', $subject) ||
            $this->detectPredicateMethod('have', 'has', $subject)) {
            return true;
        }
        return false;
    }

    private function detectPredicateMethod($indicative, $present, $subject)
    {
        if (preg_match('/^' . $indicative . '_(.*)/', $this->name, $matches) &&
            method_exists($subject, $present . $matches[1])) {
            return $present . $matches[1];
        }
        return false;
    }


}