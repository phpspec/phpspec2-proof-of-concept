<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\BooleanNotEqualException;

class TrueMatcher extends BasicMatcher
{
    private $actual;
    
    public function supports($name, $subject, array $arguments)
    {
        return $name === 'be_true';
    }
    
    protected function matches($subject, array $arguments)
    {
        $this->actual = $subject === false ? 'false' : gettype($subject);
        return $subject === true;
    }
    
    protected function getFailureException($name, $subject, array $arguments)
    {
        return new BooleanNotEqualException(
            'Expected true got ' . $this->actual, true, $this->actual
        );
    }
    
    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        
    }
}
