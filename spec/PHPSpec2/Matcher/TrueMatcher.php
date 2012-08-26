<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class TrueMatcher implements Specification
{
    function supports_useful_aliases()
    {
        $this->object->supports('beTrue', null, array())
                     ->shouldBeTrue();

        $this->object->supports('returnTrue', null, array())
                     ->shouldBeTrue();
    }

    function complains_when_matching_anything_different_from_true()
    {
        foreach ($this->list_of_values_with_no_true() as $value) {
            $this->object->shouldThrow('PHPSpec2\Exception\Example\BooleanNotEqualException')
                 ->during('positiveMatch', array('be_true', $value, array()));
        }
    }

    function does_not_complains_when_matching_true()
    {
        $this->trueMatcher->shouldNotThrow('PHPSpec2\Exception\Example\BooleanNotEqualException')
             ->during('positiveMatch', array('be_true', true, array()));

    }

    function complains_when_reverse_matching_true()
    {
        $this->object->shouldThrow('PHPSpec2\Exception\Example\BooleanNotEqualException')
             ->during('negativeMatch', array('be_true', true, array()));
    }

    function does_not_complains_when_reverse_matching_not_true()
    {
        foreach ($this->list_of_values_with_no_true() as $value) {
            $this->object->shouldNotThrow('PHPSpec2\Exception\Example\BooleanNotEqualException')
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
