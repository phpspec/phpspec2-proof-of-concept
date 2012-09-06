<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class ComparisonMatcher extends BasicMatcher
{
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('beLike'))
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return $subject == $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new NotEqualException(sprintf(
            '<strong>%s</strong> is <strong>not equal</strong> to expected <strong>%s</strong>, but should be.',
            $this->representer->representValue($subject),
            $this->representer->representValue($arguments[0])
        ), $subject, $arguments[0]);
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            '<strong>%s</strong> is <strong>equal</strong> to <strong>%s</strong>, but should not be.',
            $this->representer->representValue($subject),
            $this->representer->representValue($arguments[0])
        ));
    }
}
