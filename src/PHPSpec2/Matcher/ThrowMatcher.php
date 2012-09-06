<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class ThrowMatcher implements MatcherInterface
{
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
    }

    public function supports($name, $subject, array $arguments)
    {
        return 'throw' === $name;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        $verification = new Verification\PositiveThrowVerification(
            $subject, $arguments, $this->representer
        );

        return $verification;
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        $verification = new Verification\NegativeThrowVerification(
            $subject, $arguments, $this->representer
        );

        return $verification;
    }

    public function getPriority()
    {
        return 1;
    }
}
