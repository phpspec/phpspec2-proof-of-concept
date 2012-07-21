<?php

namespace Spec\PHPSpec2;

class Example
{
    private $string;
    private $items = array();

    public function __construct($string, array $items)
    {
        $this->string = $string;
        $this->items  = $items;
    }

    public function getString()
    {
        return $this->string;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function isFull()
    {
        return false;
    }
}

use PHPSpec2\SpecificationInterface;

class ExampleSpec implements SpecificationInterface
{
    function described_with()
    {
        $this->object->is_an_instance_of('Spec\PHPSpec2\Example', array(
            'hello, world', array(1, 2, 3)
        ));
    }

    function returns_correct_string($example)
    {
        $this->object->getString()->should()->be_equal('hello, world');
        $this->object->getString()->should_eql('hello, world');

        $this->object->string->should_be_equal('hello, world');
    }

    function has_correct_items_count($example)
    {
        $this->object->getItems()->should()->have(3);
        $this->object->getItems()->should_have(3);
        $this->object->getItems()->should_contain(3);

        $this->object->items->should_have(3);

        $this->object->should_have(3, 'items');
    }

    function should_not_be_full_yet($example)
    {
        $this->object->should_not_be('full');
    }
}
