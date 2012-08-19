<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;
use PHPSpec2\Exception\Example\FailureException;

class PredicateMatcher extends BasicMatcher
{
    private $name;
    private $method;

    public function supports($name, $subject, array $arguments)
    {
        $this->name = $name;
        return is_object($subject) &&
               $this->method = $this->detectMethod($subject);
    }

    protected function matches($name, $subject, array $arguments)
    {
        if (!$this->method) {
            throw new ExampleException(
                "Before using PredicateMatcher, check if it supports the alias"
            );
        }

        return call_user_func_array(array($subject, $this->method), $arguments);
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(
            "Expected {$this->method} to return true, got false. Using ($name)"
        );
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(
            "Expected $this->method not to return true, got true. Using ($name)"
        );
        
    }
    
    private function detectMethod($subject)
    {
        if ($method = $this->detectPredicateMethod('be', 'is', $subject)) {
            return $method;
        } elseif ($method = $this->detectPredicateMethod('have', 'has', $subject)) {
            return $method;
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