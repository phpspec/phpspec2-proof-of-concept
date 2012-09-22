<?php

namespace spec\PHPSpec2\Formatter\Representer;

use PHPSpec2\ObjectBehavior;
use stdClass;

class BasicRepresenter extends ObjectBehavior
{
    function it_should_support_all_types()
    {
        foreach ($this->allTypes() as $value) {
            $this->representValue($value[0])->shouldBe($value[1]);
        }
    }

    private function allTypes()
    {
        return array(
            'string' => array('some_string', '"some_string"'),
            'long_string' => array('some_string_longer_than_thirty_characters', '[string]'),
            'integer' => array(42, 'integer(42)'),
            'object' => array(new stdClass, 'object(stdClass)'),
            'array'  => array(array(1, 2, 3), 'array(3)'),
            'boolean' => array(true, 'true'),
            'closure' => array(function () { return 42; }, '[closure]')
        );
    }
}
