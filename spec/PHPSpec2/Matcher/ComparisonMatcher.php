<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class ComparisonMatcher implements Specification
{
    private static $NO_ARGUMENTS = array();

    function it_should_support_all_aliases_for_allKindsOfSubjects()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            $this->supportsAliasForAllKinds($alias, $this->object);
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_empty_string_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_not_empty_string_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_empty_string_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_zero_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_null_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_false_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_not_match_non_empty_different_value()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $value) {

                // skip true
                if ($value === true) continue;

                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_empty_string_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_not_empty_string_using_comparison_operator($matcher)
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_empty_string_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_zero_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_null_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_false_with_emptish_values_using_comparison_operator()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_on_non_empty_different_value()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $value) {

                // skip true
                if ($value === true) continue;

                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    function match_throws_type_specific_failure_exception()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $type => $value) {

                // we need a booleans not equal exception
                if ($value === true) $value = false;

                $this->object->shouldThrow(
                    $this->failureExceptionFor($type)
                )->during('positiveMatch', array($alias, $value, array('different_value')));
            }
        }
    }

    function it_mismatch_throws_with_type_specific_message()
    {
        foreach ($this->allComparisonMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $type => $value) {
                $this->object->shouldThrow(
                    'PHPSpec2\Exception\Example\FailureException',
                    $this->mismatchMessageFor($type)
                )->during('negativeMatch', array($alias, $value, array($value)));
            }
        }
    }

    private function supportsAliasForAllKinds($alias, $matcher)
    {
        foreach ($this->allKindsOfSubjects() as $kind => $subject) {
            $matcher->supports($alias, $subject, self::$NO_ARGUMENTS)->shouldBeTrue();
        }
    }

    private function allKindsOfSubjects()
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

    private function phpEmptishValues()
    {
        return array(
            "",
            0,
            false,
            null
        );
    }

    private function allComparisonMatcherAliases()
    {
        return array(
            'beLike'
        );
    }

    private function failureExceptionFor($type)
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

    private function mismatchMessageFor($type)
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
