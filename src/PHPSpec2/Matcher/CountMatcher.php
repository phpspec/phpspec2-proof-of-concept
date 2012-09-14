<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class CountMatcher extends BasicMatcher
{
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return 'haveCount' === $name
            && 1 == count($arguments)
            && is_array($subject) || $subject instanceof \Countable
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return $arguments[0] === count($subject);
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Expected <value>%s</value> to have <value>%d</value> items, but got <value>%d</value>.',
            $this->representer->representValue($subject), $arguments[0], count($subject)
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected <value>%s</value> to have <value>%d</value> items, but got it.',
            $this->representer->representValue($subject), $arguments[0]
        ));
    }
}
