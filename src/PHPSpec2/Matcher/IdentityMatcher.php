<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class IdentityMatcher extends BasicMatcher
{
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('return', 'be', 'equal', 'beEqualTo'))
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return $subject === $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Expected <value>%s</value>, but got <value>%s</value>.',
            $this->representer->representValue($arguments[0]),
            $this->representer->representValue($subject)
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected <value>%s</value>, but got one.',
            $this->representer->representValue($subject)
        ));
    }
}
