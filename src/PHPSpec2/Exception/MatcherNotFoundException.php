<?php

namespace PHPSpec2\Exception;

class MatcherNotFoundException extends Exception
{
    public function __construct($matcher, $subject, array $arguments)
    {
        parent::__construct(sprintf(
            'Matcher <value>%s</value> not found for <value>%s</value>. Have you registered it properly?',
            $matcher,
            gettype($subject)
        ));
    }
}
