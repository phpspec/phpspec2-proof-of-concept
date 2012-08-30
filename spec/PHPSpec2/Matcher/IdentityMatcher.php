<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class IdentityMatcher implements Specification
{
    function it_should_support_all_aliases_for_allKindsOfSubjects()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            $this->supportsAliasForAllKinds($alias, $this->object);
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_empty_string_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                ->during('positiveMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_matches_not_empty_string_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('positiveMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_not_matches_empty_string_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === '') continue;
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_not_matches_zero_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === 0) continue;
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_not_matches_null_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === null) continue;
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_matches_false_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === false) continue;
                $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('positiveMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Positive Matching"
     */
    function it_does_not_match_non_empty_different_value()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
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
    function it_mismatches_empty_string_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, '', array('')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_not_empty_string_using_identity_operator($matcher)
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
                    ->during('negativeMatch', array($alias, 'chuck', array('chuck')));
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_empty_string_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === '') continue;
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, '', array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_zero_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === 0) continue;
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 0, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_null_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === null) continue;
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, null, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_false_with_emptish_values_using_identity_operator()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->phpEmptishValues() as $empty) {
                if ($empty === false) continue;
                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, false, array($empty)));
            }
        }
    }

    /**
     * @Context "Negative Matching"
     */
    function it_mismatches_on_non_empty_different_value()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $value) {

                // skip true
                if ($value === true) continue;

                $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
                        ->during('negativeMatch', array($alias, 'different_value',array($value)));
            }
        }
    }

    function its_match_throws_type_specific_failure_exception()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $type => $value) {

                // we need a booleans not equal exception
                if ($value === true) $value = false;

                $this->object->shouldThrow(
                    $this->failureExceptionFor($type)
                )->during('positiveMatch', array($alias, $value, array('different_value')));
            }
        }
    }

    function its_mismatch_throws_with_type_specific_message()
    {
        foreach ($this->allIdentityMatcherAliases() as $alias) {
            foreach ($this->allKindsOfSubjects() as $type => $value) {
                $this->object->shouldThrow(
                    'PHPSpec2\Exception\Example\FailureException',
                    $this->missmatchMessageFor($type)
                )->during('negativeMatch', array($alias, $value, array($value)));
            }
        }
    }

    private function supportsAliasForAllKinds($alias, $matcher)
    {
        foreach ($this->allKindsOfSubjects() as $kind => $subject) {
            $matcher->supports($alias, $subject, array(1))->shouldReturnTrue();
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

    private function allIdentityMatcherAliases()
    {
        return array(
            'equal', 'return', 'beEqualTo', 'be'
        );
    }

    private function failureExceptionFor($type)
    {
        $namespace = "PHPSpec2\\Exception\\Example\\";
        $exceptions = array(
            'string'   => 'FailureException',
            'integer'  => 'FailureException',
            'object'   => 'FailureException',
            'array'    => 'FailureException',
            'boolean'  => 'FailureException',
            'resource' => 'FailureException'
        );
        return $namespace . $exceptions[$type];
    }

    private function missmatchMessageFor($type)
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
