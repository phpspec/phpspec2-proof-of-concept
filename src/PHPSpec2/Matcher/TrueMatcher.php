<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class TrueMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('beTrue', 'returnTrue'));
    }

    protected function matches($subject, array $arguments)
    {
        return true === $subject;
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException('Expected TRUE, but got FALSE.');
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException('Expected FALSE, but got TRUE.');
    }
}
