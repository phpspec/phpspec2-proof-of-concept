<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;

class TrueMatcher extends ObjectBehavior
{
    function it_supports_useful_aliases()
    {
        $this->supports('beTrue', null, array())->shouldBeTrue();
        $this->supports('returnTrue', null, array())->shouldBeTrue();
    }

    function it_complains_when_matching_anything_different_from_true()
    {
        foreach ($this->listOfNotTrueValues() as $value) {
            $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                ->during('positiveMatch', array('be_true', $value, array()));
        }
    }

    function it_does_not_complains_when_matching_true()
    {
        $this->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('be_true', true, array()));

    }

    function it_complains_when_reverse_matching_true()
    {
        $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('negativeMatch', array('be_true', true, array()));
    }

    function it_does_not_complains_when_reverse_matching_not_true()
    {
        foreach ($this->listOfNotTrueValues() as $value) {
            $this->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                ->during('negativeMatch', array('be_true', $value, array()));
        }

    }

    private function listOfNotTrueValues()
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
