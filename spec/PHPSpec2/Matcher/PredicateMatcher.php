<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class PredicateMatcher implements Specification
{
    /**
     * @param ObjectStub $subject mock of stdClass
     */
    function infers_matcher_alias_name_from_methods_prefixed_with_is($subject)
    {
        $subject->isValid()->will_return(true);
        
        $this->object->supports('be_valid', $subject, array())->should_return_true();
    }
}