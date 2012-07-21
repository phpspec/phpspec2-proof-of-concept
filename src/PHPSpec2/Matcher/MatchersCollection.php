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

    public function getAll()
    {
        return $this->matchers;
    }

    public function findFirst($subject, $keyword, array $arguments)
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->supports($subject, $keyword, $arguments)) {
                return $matcher;
            }
        }

        throw new MatcherNotFoundException($keyword);
    }
}
