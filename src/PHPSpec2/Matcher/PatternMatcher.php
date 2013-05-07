<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\Example\FailureException;

class PatternMatcher extends BasicMatcher
{
    private $presenter;

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

    public function supports($name, $subject, array $arguments)
    {
        return 'matchPattern' === $name
            && 1 == count($arguments);
    }

    protected function matches($subject, array $arguments)
    {
        return (bool) preg_match($arguments[0], $subject);
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            "%s doesn't match %s, but it should.",
            $this->presenter->presentString($subject),
            $this->presenter->presentString($arguments[0])
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            "%s matches %s, but it shouldn't.",
            $this->presenter->presentString($subject),
            $this->presenter->presentString($arguments[0])
        ));
    }
}
