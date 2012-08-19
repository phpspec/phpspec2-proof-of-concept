<?php

namespace PHPSpec2\Matcher;

class CountMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments) {}
    protected function matches($name, $subject, array $arguments) {}
    protected function getFailureException($name, $subject, array $arguments) {}
    protected function getNegativeFailureException($name, $subject, array $arguments) {}
}
