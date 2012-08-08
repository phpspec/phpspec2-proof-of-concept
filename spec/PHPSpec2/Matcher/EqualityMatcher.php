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

    function should_support_all_aliases_for_all_kinds_of_subjects($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            $this->supports_alias_for_all_kinds($alias, $matcher);
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_empty_string_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_not_empty_string_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_empty_string_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_zero_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_null_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_false_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_not_match_non_empty_different_value($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $value) {

                // skip true
                if ($value === true) continue;

                $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_empty_string_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_not_empty_string_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_empty_string_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_zero_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_null_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_false_with_emptish_values_using_comparison_operator($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                $matcher->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_on_non_empty_different_value($matcher)
    {
        foreach ($this->all_equality_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $value) {

                // skip true
                if ($value === true) continue;

                $matcher->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 'different_value',array($value)));
            }
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

    private function all_equality_matcher_aliases()
    {
        return array(
            'equal', 'return', 'be_equal_to', 'be'
        );
    }
}
