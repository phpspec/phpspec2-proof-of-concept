<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\MatcherNotFoundException;

class MatchersCollection
{
    private $matchers = array();

    public function add(MatcherInterface $matcher)
    {
        $this->matchers[] = $matcher;
    }

    public function find($keyword, $subject, array $arguments)
    {
        foreach ($this->matchers as $matcher) {
            if (true === $matcher->supports($keyword, $subject, $arguments)) {
                return $matcher;
            }
        }

        throw new MatcherNotFoundException($keyword);
    }

    public function getAll()
    {
        return $this->matchers;
    }
}
