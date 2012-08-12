<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class IdentityMatcher implements Specification
{
    private static $NO_ARGUMENTS = array();

    function should_support_all_aliases_for_all_kinds_of_subjects()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            $this->supports_alias_for_all_kinds($alias, $this->object);
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_empty_string_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function matches_not_empty_string_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_not_matches_empty_string_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === '') continue;
                $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_not_matches_zero_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === 0) continue;
                $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_not_matches_null_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === null) continue;
                $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_matches_false_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === false) continue;
                $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function does_not_match_non_empty_different_value()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $value) {

                // skip true
                if ($value === true) continue;

                $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_empty_string_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_not_empty_string_using_identity_operator($matcher)
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_empty_string_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === '') continue;
                $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_zero_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === 0) continue;
                $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_null_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === null) continue;
                $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_false_with_emptish_values_using_identity_operator()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->php_emptish_values() as $empty) {
                if ($empty === false) continue;
                $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function mismatches_on_non_empty_different_value()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $value) {

                // skip true
                if ($value === true) continue;

                $this->object->should_not_throw('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    function match_throws_type_specific_failure_exception()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $type => $value) {

                // we need a booleans not equal exception
                if ($value === true) $value = false;

                $this->object->should_throw(
                    $this->failure_exception_for($type)
                )->during('positiveMatch', array($alias, $value, array('different_value')));
            }
        }
    }

    function mismatch_throws_with_type_specific_message()
    {
        foreach ($this->all_identity_matcher_aliases() as $alias) {
            foreach ($this->all_kinds_of_subjects() as $type => $value) {
                $this->object->should_throw(
                    'PHPSpec2\Exception\Example\FailureException',
                    $this->mismatch_message_for($type)
                )->during('negativeMatch', array($alias, $value, array($value)));
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

    private function all_identity_matcher_aliases()
    {
        return array(
            'equal', 'return', 'be_equal_to', 'be_equal'
        );
    }

    private function failure_exception_for($type)
    {
        $namespace = "PHPSpec2\\Exception\\Example\\";
        $exceptions = array(
            'string'   => 'StringsNotEqualException',
            'integer'  => 'IntegersNotEqualException',
            'object'   => 'ObjectsNotEqualException',
            'array'    => 'ArraysNotEqualException',
            'boolean'  => 'BooleansNotEqualException',
            'resource' => 'ResourcesNotEqualException'
        );
        return $namespace . $exceptions[$type];
    }

    private function mismatch_message_for($type)
    {
        $messages = array(
            'string' => 'Strings are equal, but they shouldn\'t be',
            'integer' => 'Integers are equal, but they shouldn\'t be',
            'object' => 'Objects are equal, but they shouldn\'t be',
            'array'  => 'Arrays are equal, but they shouldn\'t be',
            'boolean' => 'Booleans are equal, but they shouldn\'t be',
            'resource' => 'Resources are equal, but they shouldn\'t be'
        );
        return $messages[$type];
    }
}
