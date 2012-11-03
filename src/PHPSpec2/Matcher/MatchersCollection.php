<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\MatcherNotFoundException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class MatchersCollection
{
    private $presenter;
    private $matchers = array();
    private $sorted   = false;

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

    public function add(MatcherInterface $matcher)
    {
        $this->matchers[] = $matcher;
        $this->sorted     = false;
    }

    public function find($keyword, $subject, array $arguments)
    {
        foreach ($this->getAll() as $matcher) {
            if (true === $matcher->supports($keyword, $subject, $arguments)) {
                return $matcher;
            }
        }

        throw new MatcherNotFoundException(
            sprintf('No %s(%s) matcher found for %s.',
                $this->presenter->presentString($keyword),
                $this->presenter->presentValue($arguments),
                $this->presenter->presentValue($subject)
            ),
            $keyword, $subject, $arguments
        );
    }

    public function getAll()
    {
        if (!$this->sorted) {
            usort($this->matchers, function($matcher1, $matcher2) {
                if ($matcher1->getPriority() === $matcher2->getPriority()) {
                    return 0;
                }
                return ($matcher1->getPriority() > $matcher2->getPriority()) ? -1 : 1;
            });

            $this->sorted = true;
        }

        return $this->matchers;
    }
}
