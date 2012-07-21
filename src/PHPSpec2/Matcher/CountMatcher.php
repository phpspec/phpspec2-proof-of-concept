<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

use Countable;

class CountMatcher implements MatcherInterface
{
    public function supports($subject, $keyword, array $arguments)
    {
        return in_array($keyword, array('contain', 'have'))
            && (count($arguments) > 0 && count($arguments) < 3);
    }

    public function positive($subject, array $arguments)
    {
        if (isset($arguments[1])) {
            $getter  = 'get'.ucfirst($arguments[1]);
            $subject = $subject->$getter();
        }

        if ($arguments[0] !== count($subject)) {
            throw new ExampleException(sprintf(
                'Expected to have %d items in %s, got %d',
                $arguments[0],
                gettype($subject),
                count($subject)
            ));
        }
    }

    public function negative($subject, array $arguments)
    {
        if (isset($arguments[1])) {
            $getter  = 'get'.ucfirst($arguments[1]);
            $subject = $subject->$getter();
        }

        if ($arguments[0] === count($subject->getsubjectSubject())) {
            throw new ExampleException(sprintf(
                'Expected to not have %d items in %s, got',
                $arguments[0],
                count($subject->getsubjectSubject())
            ));
        }
    }
}
