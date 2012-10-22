<?php

namespace spec\PHPSpec2\Initializer;

use PHPSpec2\ObjectBehavior;

class FunctionParametersReader extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function let($example)
    {
        $example->getFunction()->willReturn(null);
        $example->getPreFunctions()->willReturn(array());
        $example->getPostFunctions()->willReturn(array());
    }

    /**
     * @param ReflectionFunction  $function
     * @param ReflectionParameter $param1
     * @param ReflectionParameter $param2
     */
    function it_should_read_example_arguments($example, $function, $param1, $param2)
    {
        $example->getFunction[-1]->willReturn($function)->shouldBeCalled();

        $function->getParameters->willReturn(array($param1, $param2));
        $function->getDocComment()->willReturn(false);

        $param1->getName()->willReturn('param1');
        $param2->getName()->willReturn('param2');

        $this->getParameters($example)->shouldReturn(array('param1' => null, 'param2' => null));
    }

    /**
     * @param ReflectionFunction  $function
     * @param ReflectionParameter $param1
     * @param ReflectionParameter $param2
     */
    function it_should_return_function_type_if_docBlocked($example, $function, $param1, $param2)
    {
        $example->getFunction[-1]->willReturn($function)->shouldBeCalled();

        $function->getParameters->willReturn(array($param1, $param2));
        $function->getDocComment()->willReturn("/**\n * @param class \$param2\n */");

        $param1->getName()->willReturn('param1');
        $param2->getName()->willReturn('param2');

        $this->getParameters($example)->shouldReturn(array('param2' => 'class', 'param1' => null));
    }

    /**
     * @param ReflectionFunction  $func1
     * @param ReflectionFunction  $func2
     * @param ReflectionParameter $param1
     * @param ReflectionParameter $param2
     */
    function it_should_read_preFunctions_arguments($example, $func1, $func2, $param1, $param2)
    {
        $example->getPreFunctions[-1]->willReturn(array($func1, $func2));

        $func1->getParameters->willReturn(array($param1))->shouldBeCalled();
        $func1->getDocComment()->willReturn(false)->shouldBeCalled();

        $func2->getParameters->willReturn(array($param2))->shouldBeCalled();
        $func2->getDocComment()->willReturn(false)->shouldBeCalled();

        $param1->getName()->willReturn('param1');
        $param2->getName()->willReturn('param2');

        $this->getParameters($example)->shouldReturn(array('param1' => null, 'param2' => null));
    }

    /**
     * @param ReflectionFunction  $func1
     * @param ReflectionFunction  $func2
     * @param ReflectionParameter $param1
     * @param ReflectionParameter $param2
     */
    function it_should_read_postFunctions_arguments($example, $func1, $func2, $param1, $param2)
    {
        $example->getPostFunctions[-1]->willReturn(array($func1, $func2));

        $func1->getParameters->willReturn(array($param1))->shouldBeCalled();
        $func1->getDocComment()->willReturn(false)->shouldBeCalled();

        $func2->getParameters->willReturn(array($param2))->shouldBeCalled();
        $func2->getDocComment()->willReturn(false)->shouldBeCalled();

        $param1->getName()->willReturn('param1');
        $param2->getName()->willReturn('param2');

        $this->getParameters($example)->shouldReturn(array('param1' => null, 'param2' => null));
    }

    /**
     * @param ReflectionFunction  $func1
     * @param ReflectionFunction  $func2
     * @param ReflectionParameter $param1
     * @param ReflectionParameter $param2
     */
    function it_should_not_override_arguments($example, $func1, $func2, $param1, $param2)
    {
        $example->getPreFunctions[-1]->willReturn(array($func1));
        $example->getFunction[-1]->willReturn($func2);

        $func1->getParameters->willReturn(array($param1))->shouldBeCalled();
        $func1->getDocComment()->willReturn("/**\n * @param class \$param1\n */")->shouldBeCalled();

        $func2->getParameters->willReturn(array($param1, $param2))->shouldBeCalled();
        $func2->getDocComment()->willReturn(false)->shouldBeCalled();

        $param1->getName()->willReturn('param1');
        $param2->getName()->willReturn('param2');

        $this->getParameters($example)->shouldReturn(array('param1' => 'class', 'param2' => null));
    }
}
