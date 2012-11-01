<?php

namespace spec\PHPSpec2\Extension;

use PHPSpec2\ObjectBehavior;

class Configuration extends ObjectBehavior
{
    /**
     * @param PHPSpec2\ServiceContainer     $container
     * @param PHPSpec2\Extension\YamlParser $parser
     */
    function let($container, $parser)
    {
        $this->beConstructedWith($container, $parser);
    }

    function it_should_set_parameters_from_config($container, $parser)
    {
        $parser->parse('phpspec.yml')->willReturn(array(
            'some' => array(
                'deep' => array(
                    'parameter' => false
                )
            ),
            'some.simple.parameter' => 123,
            'some_array' => array(1, 2, 3)
        ));

        $container->set('some', array('deep' => array('parameter' => false)))->shouldBeCalled();
        $container->set('some.simple.parameter', 123)->shouldBeCalled();
        $container->set('some_array', array(1, 2, 3))->shouldBeCalled();

        $this->read('phpspec.yml');
    }
}
