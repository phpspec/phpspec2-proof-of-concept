<?php

namespace PHPSpec2\Matcher;

class ObjectStatePredicateMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return 0 === strpos($name, 'be_')
            && is_object($subject)
            && strlen($name) > 3;
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