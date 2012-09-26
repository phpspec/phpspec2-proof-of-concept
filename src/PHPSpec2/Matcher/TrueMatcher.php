<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Formatter\Presenter\StringPresenter;

class TrueMatcher extends BasicMatcher
{
    private $presenter;

    public function __construct(PresenterInterface $presenter = null)
    {
        $this->presenter = $presenter ?: new StringPresenter;;
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
            'Expected true, but got %s.',
            $this->presenter->presentValue($subject)
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException('Not expected true, but got it.');
    }
}
