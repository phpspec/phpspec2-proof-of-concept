<?php

namespace PHPSpec2\Stub;

use PHPSpec2\Matcher\MatchersCollection;

class Verification
{
    private $subject;
    private $matchers;
    private $positive;

    public function __construct($subject, MatchersCollection $matchers, $positive)
    {
        $this->subject  = $subject;
        $this->matchers = $matchers;
        $this->positive = $positive;
    }

    public function __call($name, array $arguments = array())
    {
        $matcher = $this->matchers->find($name, $this->subject, $arguments);

        if ($this->positive) {
            return $matcher->positiveMatch($name, $this->subject, $arguments);
        }

        return $matcher->negativeMatch($name, $this->subject, $arguments);
    }
}
