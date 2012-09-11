<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\Specification;

class Prophet implements Specification
{
    function described_with($collection)
    {
        $collection->isAMockOf('PHPSpec2\Matcher\MatchersCollection');
        $subject = new Fake;
        $this->prophet->isAnInstanceOf('PHPSpec2\Prophet\Prophet', array(
            $subject, $collection
        ));
    }

    function it_calls_magic_method_of_subject_if_one_exists()
    {
        $this->prophet->foo('bar')->shouldReturn('bar');
    }
}

class Fake
{
    public function __call($name, $args = array())
    {
        return $args[0];
    }
}