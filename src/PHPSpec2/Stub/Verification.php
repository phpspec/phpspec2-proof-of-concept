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
        $matcher = $this->matchers->findFirst($this->subject, $name, $arguments);

        if ($this->positive) {
            return $matcher->positive($this->subject, $arguments);
        }

        return $matcher->negative($this->subject, $arguments);
    }
}
