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
}

use PHPSpec2\SpecificationInterface;

class ExampleSpec implements SpecificationInterface
{
    function described_with($example)
    {
        $example->is_an_instance_of('Spec\PHPSpec2\Example', array('hello, world', array(1, 2, 3)));
    }

    function returns_correct_string($example)
    {
        $example->getString()->should()->be_equal('hello, world');
        $example->getString()->should_eql('hello, world');

        $example->string->should_be_equal('hello, world');
    }

    function has_correct_items_count($example)
    {
        $example->getItems()->should()->have(3);
        $example->getItems()->should_have(3);
        $example->getItems()->should_contain(3);

        $example->items->should_have(3);

        $example->should_have(3, 'items');
    }
}
