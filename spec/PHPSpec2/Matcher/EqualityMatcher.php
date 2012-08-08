<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class EqualityMatcher implements Specification
{
    private static $NO_ARGUMENTS = array();

    function described_with($matcher)
    {
        $matcher->is_an_instance_of('PHPSpec2\Matcher\EqualityMatcher');
    }

    function should_support_the_be_alias_matcher_for_all_kinds_of_subjects($matcher)
    {
        $this->supports_alias_for_all_kinds('be', $matcher);
    }

    function should_support_the_be_equal_to_alias_for_all_kinds_of_subjects($matcher)
    {
        $this->supports_alias_for_all_kinds('be_equal_to', $matcher);
    }

    function should_support_return_alias_for_all_kinds_of_subjects($matcher)
    {
        $this->supports_alias_for_all_kinds('return', $matcher);
    }

    function should_support_equal_alias_for_all_kinds_of_subjects($matcher)
    {
        $this->supports_alias_for_all_kinds('equal', $matcher);
    }

    function matches_empty_string_using_comparison_operator($matcher)
    {
        $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                ->during('positiveMatch', array('equal', '', array('')));
    }

    function matches_not_empty_string_using_comparison_operator($matcher)
    {
        $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                ->during('positiveMatch', array('equal', 'chuck', array('chuck')));
    }

    function matches_empty_string_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->php_emptish_values() as $empty) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array('equal', '', array($empty)));
        }
    }

    function matches_zero_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->php_emptish_values() as $empty) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array('equal', 0, array($empty)));
        }
    }

    function matches_null_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->php_emptish_values() as $empty) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array('equal', null, array($empty)));
        }
    }

    function matches_false_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->php_emptish_values() as $empty) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array('equal', false, array($empty)));
        }
    }

    function does_not_match_non_empty_different_value($matcher)
    {
        foreach ($this->all_kinds_of_subjects() as $value) {

            // skip true
            if ($value === true) continue;

            $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array('equal', 'different_value',array($value)));
        }
    }

    private function supports_alias_for_all_kinds($alias, $matcher)
    {
        foreach ($this->all_kinds_of_subjects() as $kind => $subject) {
            $matcher->supports($alias, $subject, self::$NO_ARGUMENTS)->should_be_true();
        }
    }

    private function all_kinds_of_subjects()
    {
        return array(
            'string' => 'some_string',
            'integer' => 42,
            'object' => new stdClass,
            'array'  => array(),
            'boolean' => true,
            'resource' => STDIN
        );
    }

    private function php_emptish_values()
    {
        return array(
            "",
            0,
            false,
            null
        );
    }
}
