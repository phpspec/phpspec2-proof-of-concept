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

    public function find($subject, $keyword)
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->supports($subject, $keyword)) {
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
