<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Formatter\Presenter\StringPresenter;

class ComparisonMatcher extends BasicMatcher
{
    private $presenter;

    public function __construct(PresenterInterface $presenter = null)
    {
        $this->presenter = $presenter ?: new StringPresenter;
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
            'Expected %s, but got %s.',
            $this->presenter->presentValue($arguments[0]),
            $this->presenter->presentValue($subject)
        ), $arguments[0], $subject);
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected %s, but got one.',
            $this->presenter->presentValue($subject)
        ));
    }
}
