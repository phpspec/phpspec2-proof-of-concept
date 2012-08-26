<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class PredicateMatcher implements Specification
{
    function infers_matcher_alias_name_from_methods_prefixed_with_is()
    {
        $subject = new \ReflectionClass($this);

        // $subject->isAbstract(...)
        $this->object->supports('be_abstract', $subject, array())->shouldReturnTrue();
    }

    function infers_matcher_alias_name_from_methods_prefixed_with_has()
    {
        $subject = new \ReflectionClass($this);

        // $subject->hasMethod(...)
        $this->object->supports('have_method', $subject, array())->shouldReturnTrue();
    }

    function matches_is_method_against_true()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('be_abstract', $subject, array());

        // $subject->isAbstract(...)
        $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('be_abstract', $subject, array()));
    }

    function matches_has_method_against_true()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('have_method', $subject, array());

        // $subject->hasMethod(...)
        $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('have_method', $subject, array('unknown_method')));
    }
}
