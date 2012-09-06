<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class TrueMatcher extends BasicMatcher
{
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
    }

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
        return new FailureException(sprintf(
            'Expected <value>true</value>, but got <value>%s</value>.',
            $this->representer->representValue($subject)
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException('Not expected <value>true</value>, but got it.');
    }
}
