<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\Specification;
use PHPSpec2\Prophet\ArgumentsResolver;

class ObjectProphet implements Specification
{
    function described_with($matchers, $mocker, $resolver)
    {
        $matchers->isAMockOf('PHPSpec2\Matcher\MatchersCollection');
        $mocker->isAMockOf('PHPSpec2\Mocker\Mockery\Mocker');

        $subject  = new Fake;
        $resolver = new ArgumentsResolver;
        $this->objectProphet->isAnInstanceOf('PHPSpec2\Prophet\ObjectProphet', array(
            $subject, $matchers, $resolver
        ));
    }

    function it_calls_magic_method_of_subject_if_one_exists()
    {
        $this->objectProphet->foo('bar')->shouldReturn('bar');
    }
}

class Fake
{
    public function __call($name, $args = array())
    {
        return $args[0];
    }
}
