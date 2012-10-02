<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Formatter\Presenter\StringPresenter;

class CountMatcher extends BasicMatcher
{
    private $presenter;

    public function __construct(PresenterInterface $presenter = null)
    {
        $this->presenter = $presenter ?: new StringPresenter;
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
            'Expected %s to have %s items, but got %s.',
            $this->presenter->presentValue($subject),
            $this->presenter->presentString(intval($arguments[0])),
            $this->presenter->presentString(count($subject))
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected %s to have %s items, but got it.',
            $this->presenter->presentValue($subject),
            $this->presenter->presentString(intval($arguments[0]))
        ));
    }
}
