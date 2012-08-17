<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class TrueMatcher implements Specification
{
    function supports_useful_aliases()
    {
        $this->object->supports('be_true', null, array())
                     ->should_be_true();
                     
        $this->object->supports('return_true', null, array())
                     ->should_be_true();
    }

    function complains_when_matching_anything_different_from_true()
    {
        foreach ($this->list_of_values_with_no_true() as $value) {
            $this->object->should_throw('PHPSpec2\Exception\Example\BooleanNotEqualException')
                 ->during('positiveMatch', array('be_true', $value, array()));
        }
    }

    function does_not_complains_when_matching_true()
    {
        $this->trueMatcher->should_not_throw('PHPSpec2\Exception\Example\BooleanNotEqualException')
             ->during('positiveMatch', array('be_true', true, array()));
        
    }

    function complains_when_reverse_matching_true()
    {
        $this->object->should_throw('PHPSpec2\Exception\Example\BooleanNotEqualException')
             ->during('negativeMatch', array('be_true', true, array()));
    }

    function does_not_complains_when_reverse_matching_not_true()
    {
        foreach ($this->list_of_values_with_no_true() as $value) {
            $this->object->should_not_throw('PHPSpec2\Exception\Example\BooleanNotEqualException')
                 ->during('negativeMatch', array('be_true', $value, array()));
        }
        
    }

    private function list_of_values_with_no_true()
    {
        return array(
            1,
            array(),
            new \stdClass,
            STDOUT,
            false,
            null
        );
    }
}