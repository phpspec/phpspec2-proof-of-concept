<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class PredicateMatcher implements Specification
{
    function infers_matcher_alias_name_from_methods_prefixed_with_is()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('be_abstract', $subject, array())->should_return_true();
    }
    
    function infers_matcher_alias_name_from_methods_prefixed_with_has()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('have_method', $subject, array())->should_return_true();
    }
}