<?php

namespace spec\PHPSpec2\Stub;

use PHPSpec2\Specification;

class ObjectStub implements Specification
{
    function described_with($collection)
    {
        $collection->isAMockOf('PHPSpec2\Matcher\MatchersCollection');
        $subject = new Fake;
        $this->objectStub->isAnInstanceOf('PHPSpec2\Stub\ObjectStub', array(
            $subject, $collection
        ));
    }

    function it_calls_magic_method_of_subject_if_one_exists()
    {
        $this->objectStub->foo('bar')->shouldReturn('bar');
    }
}

class Fake
{
    public function __call($name, $args = array())
    {
        return $args[0];
    }
}