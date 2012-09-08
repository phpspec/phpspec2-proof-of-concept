<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class ContainerPredicateMatcher implements MatcherInterface
{
    public function supports($name, $subject, array $arguments)
    {
        return 0 === strpos($name, 'have_')
            && is_object($subject);
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        $method = 'has' . substr(str_replace('_', '', $name), 4);
        if (call_user_func_array(array($subject, $method), $arguments) !== true) {
            throw $this->getFailureException($name, $subject, $arguments);            
        }

        return $subject;
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        $method = 'has' . substr(str_replace('_', '', $name), 4);
        if (call_user_func_array(array($subject, $method), $arguments) === true) {
            throw $this->getNegativeFailureException($name, $subject, $arguments);
        }

        return $subject;
    }

    public function getFailureException($name, $subject, array $arguments)
    {
        $method = preg_replace(
            array('#(_)([A-Za-z]{1})#e','#(^[A-Za-z]{1})#e'),
            array("strtoupper('\\2')","strtoupper('\\1')"),
            $name
        );
        $method = 'has' . substr(str_replace('_', '', $method), 4);
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
        $method = 'has' . substr(str_replace('_', '', $method), 4);
        return new FailureException(
            "Expected $method not to return true, got true. Using ($name)"
        );
    }
}