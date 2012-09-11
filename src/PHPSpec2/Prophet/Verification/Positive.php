<?php

namespace PHPSpec2\Prophet\Verification;

use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Prophet\ArgumentsResolver;

class Positive
{
    private $subject;
    private $matchers;
    private $resolver;

    public function __construct($subject, MatchersCollection $matchers, ArgumentsResolver $resolver)
    {
        $this->subject  = $subject;
        $this->matchers = $matchers;
        $this->resolver = $resolver;
    }

    public function __call($name, array $arguments = array())
    {
        $subject   = $this->resolver->resolveSingle($this->subject);
        $arguments = $this->resolver->resolve($arguments);

        $matcher = $this->matchers->find($name, $subject, $arguments);

        return $matcher->positiveMatch($name, $subject, $arguments);
    }
}
