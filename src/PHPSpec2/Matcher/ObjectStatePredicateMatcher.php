<?php

namespace PHPSpec2\Matcher;

class ObjectStatePredicateMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return 0 === strpos($name, 'be_');
    }

    public function matches($subject, array $arguments)
    {
        
    }

    public function getFailureException($name, $subject, array $arguments)
    {
        
    }

    public function getNegativeFailureException($name, $subject, array $arguments)
    {
        
    }
}