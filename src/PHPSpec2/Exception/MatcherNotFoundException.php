<?php

namespace PHPSpec2\Exception;

class MatcherNotFoundException extends Exception
{
    public function __construct($matcher)
    {
        parent::__construct(sprintf(
            'Matcher <value>%s</value> not found. Have you registered it properly?', $matcher
        ));
    }
}
