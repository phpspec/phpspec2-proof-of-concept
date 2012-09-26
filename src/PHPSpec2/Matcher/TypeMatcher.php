<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Formatter\Presenter\StringPresenter;

class TypeMatcher extends BasicMatcher
{
    private $presenter;

    public function __construct(PresenterInterface $presenter = null)
    {
        $this->presenter = $presenter ?: new StringPresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('beAnInstanceOf', 'returnAnInstanceOf'))
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return null !== $subject && $subject instanceof $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Expected an instance of %s, but got %s.',
            $this->presenter->presentString($arguments[0]),
            $this->presenter->presentValue($subject)
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected instance of %s, but got %s.',
            $this->presenter->presentString($arguments[0]),
            $this->presenter->presentValue($subject)
        ));
    }
}
