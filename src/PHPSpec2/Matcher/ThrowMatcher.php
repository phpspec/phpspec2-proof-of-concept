<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Looper\Looper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Formatter\Presenter\StringPresenter;

class ThrowMatcher implements MatcherInterface
{
    private $presenter;

    public function __construct(PresenterInterface $presenter = null)
    {
        $this->presenter = $presenter ?: new StringPresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return 'throw' === $name;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        $verification = new Verification\PositiveThrowVerification(
            $subject, $arguments, $this->presenter
        );

        return $verification;
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        $verification = new Verification\NegativeThrowVerification(
            $subject, $arguments, $this->presenter
        );

        return $verification;
    }

    public function getPriority()
    {
        return 1;
    }
}
