<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Example\FailureException;

class ObjectBinaryStateMatcher implements Specification
{
    function matches_call_with_be_prefix_and_single_argument_on_object()
    {
        $this->object->supports('be', $this, array('paid'))
            ->should_return(true);
    }

    function does_not_supports_otherways()
    {
        $this->object->supports('be', $this, array())
            ->should_return(false);
    }

    function positively_matches_if_subject_state_checker_returns_true()
    {
        $subject = new \ReflectionObject($this);

        $this->object
            ->should_not_throw(new FailureException())
            ->during('positiveMatch', array($subject, array('userDefined')));
    }

    function does_not_positively_matches_if_subject_state_checker_returns_false()
    {
        $subject = new \ReflectionObject($this);

        $this->object
            ->should_throw(new FailureException(
                'Expected isAbstract to return true, got false.'
            ))
            ->during('positiveMatch', array('be', $subject, array('abstract')));
    }

    function negatively_matches_if_subject_state_checker_returns_false()
    {
        $subject = new \ReflectionObject($this);

        $this->object
            ->should_not_throw(new FailureException())
            ->during('negativeMatch', array($subject, array('abstract')));
    }

    function does_not_negatively_matches_if_subject_state_checker_returns_true()
    {
        $subject = new \ReflectionObject($this);

        $this->object
            ->should_throw(new FailureException(
                'Expected isUserDefined to return false, got true.'
            ))
            ->during('negativeMatch', array('be', $subject, array('userDefined')));
    }

    /**
     * @param ObjectStub $subject mock of stdClass
     */
    function throws_exception_if_subject_state_method_does_not_exists($subject)
    {
        $this->object
            ->should_throw(new MethodNotFoundException($subject, 'isPaid'))
            ->during('positiveMatch', array('be', $subject, array('paid')));
    }
}
