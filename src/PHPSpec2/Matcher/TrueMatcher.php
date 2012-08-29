<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\BooleanNotEqualException;

class TrueMatcher extends BasicMatcher
{
    private $actual;
    private $usedAlias;

    public function supports($name, $subject, array $arguments)
    {
        $this->usedAlias = $name;
        return in_array($name, array('beTrue', 'returnTrue'));
    }

    protected function matches($subject, array $arguments)
    {
        $this->actual = $subject === false ? 'false' : gettype($subject);
        return $subject === true;
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        $type = $this->actual === false ? 'false' : gettype($this->actual);
        return new BooleanNotEqualException(
            'Expected true got ' . $type . 'using (' . $this->usedAlias . ')', true, $this->actual
        );
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new BooleanNotEqualException(
            'Expected not to be true, got true using (' . $this->usedAlias . ')', true, $this->actual
        );
    }
}
