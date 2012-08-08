<?php

namespace PHPSpec2\Matcher;

class PredicateMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        if (is_object($subject) && preg_match('/^be_(.*)/', $name, $matches)) {
            return method_exists($subject, 'is' . $matches[1]);
        }

        if (is_object($subject) && preg_match('/^have_(.*)/', $name, $matches)) {
            return method_exists($subject, 'has' . $matches[1]);
        }
    }

    protected function matches($subject, array $arguments)
    {
        
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        
    }
}