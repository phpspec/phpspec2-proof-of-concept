<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\Specification;

class ObjectProphet implements Specification
{
    /**
     * @param PHPSpec2\Matcher\MatchersCollection $matchers
     * @param PHPSpec2\Prophet\ArgumentsResolver  $resolver
     */
    function described_with($matchers, $resolver)
    {
        $this->objectProphet->instantiatedWith(new Fake, $matchers, $resolver);
    }

    function it_calls_magic_method_of_subject_if_one_exists($resolver)
    {
        $resolver->resolve(array('bar'))->willReturn(array('bar'));
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
