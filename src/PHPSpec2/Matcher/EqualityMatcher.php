<?php

namespace PHPSpec2\Matcher;

class EqualityMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments) {}
    protected function matches($subject, array $arguments) {}
    protected function getFailureException($name, $subject, array $arguments) {}
    protected function getNegativeFailureException($name, $subject, array $arguments) {}
}
