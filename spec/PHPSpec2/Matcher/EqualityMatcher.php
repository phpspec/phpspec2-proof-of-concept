<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class EqualityMatcher implements Specification
{
    private static $NO_ARGUMENTS = array();

    function all_kinds_of_subjects()
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

    function should_support_the_be_matcher_for_all_kind_of_subjects()
    {
        $this->object->is_an_instance_of('PHPSpec2\Matcher\EqualityMatcher');

        foreach ($this->all_kinds_of_subjects() as $kind => $subject) {
            $this->object->supports('be', $subject, self::$NO_ARGUMENTS)->should_be_true();
        }
    }
}
