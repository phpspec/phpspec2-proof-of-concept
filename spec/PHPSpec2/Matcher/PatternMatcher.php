<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class PatternMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    public function let($presenter)
    {
        $presenter->presentString(ANY_ARGUMENTS)->willReturnArgument();
        $presenter->presentString(ANY_ARGUMENTS)->willReturnArgument();

        $this->beConstructedWith($presenter);
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('PHPSpec2\Matcher\PatternMatcher');
    }

    public function it_should_support_match()
    {
        $this->supports('matchPattern', 'subj', array('/^jbus$/'))->shouldReturn(true);
    }

    public function it_shouldnt_support_match_if_no_pattern_is_given()
    {
        $this->supports('matchPattern', 'subj', array())->shouldReturn(false);
    }

    public function it_throws_exception_when_subject_doesnt_match_pattern_but_should()
    {
        $this->shouldThrow(
            new FailureException("subj doesn't match /^jbus$/, but it should.")
        )->duringPositiveMatch('match', 'subj', array('/^jbus$/'));
    }

    public function it_should_match_when_subject_matches_pattern()
    {
        $this->shouldNotThrow()->duringPositiveMatch('match', 'subj', array('/^subj$/'));
    }

    public function it_throws_exception_when_subject_matches_pattern_but_shouldnt()
    {
        $this->shouldThrow(
            new FailureException("subj matches /^subj$/, but it shouldn't.")
        )->duringNegativeMatch('match', 'subj', array('/^subj$/'));
    }

    public function it_should_mismatch_when_subject_doesnt_match_pattern()
    {
        $this->shouldNotThrow()->duringNegativeMatch('match', 'subj', array('/^jbus$/'));
    }
}
