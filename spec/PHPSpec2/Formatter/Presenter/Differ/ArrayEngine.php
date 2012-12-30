<?php

namespace spec\PHPSpec2\Formatter\Presenter\Differ;

use PHPSpec2\ObjectBehavior;

class ArrayEngine extends ObjectBehavior
{
    function it_should_support_arrays()
    {
        $this->supports(array('first'), array('second'))->shouldReturn(true);
    }

    function it_should_calculate_diff_without_recursion()
    {
        $this->compare(array('first'), array('second'))->shouldReturn(<<<STRING
<code>
\$expected["0"] is "first", but expected to be "second"
</code>
STRING
        );

    }

    function it_should_calculate_diff_for_associative_array()
    {
        $this->compare(array('a' => 'first'), array('a' => 'second'))->shouldReturn(<<<STRING
<code>
\$expected["a"] is "first", but expected to be "second"
</code>
STRING
        );
    }


    function it_should_calculate_diff_for_nested_array()
    {

    	$arrayExpected = array(
    		'a1' => array(
				'a2' => array(
					'a3' => array(
						'a4a' => '100', 
						'a4b' => '102'
					)
				)
			)
    	);

    	$arrayActual = array(
    		'a1' => array(
				'a2' => array(
					'a3' => array(
						'a4a' => '100', 
						'a4b' => '104'
					)
				)
			)
    	);

        $this->compare($arrayExpected, $arrayActual)->shouldReturn(<<<STRING
<code>
\$expected["a1"]["a2"]["a3"]["a4b"] is "102", but expected to be "104"
</code>
STRING
        );
    }


    function it_should_calculate_diff_for_nested_array_with_multiple_differences()
    {

    	$arrayExpected = array(
    		'a1' => array(
				'a2' => array(
					'a3' => array(
						'a4a' => '100', 
						'a4b' => '102'
					)
				)
			),
			'b',
			'c' => array(
				'c2'
			),
			array(array(array('d')))
    	);

    	$arrayActual = array(
    		'a1' => array(
				'a2' => array(
					'a3' => array(
						'a4a' => '100', 
						'a4b' => '102'
					)
				)
			),
			'd',
			'c1' => array(
				'cd'
			),
			array(array(array('s')))
    	);

        $this->compare($arrayExpected, $arrayActual)->shouldReturn(<<<STRING
<code>
\$expected["0"] is "b", but expected to be "d"
\$expected["c1"] Array does not exists, but expected to be
\$expected["1"]["0"]["0"]["0"] is "d", but expected to be "s"
\$expected["c"] Array exists, but expected not to be
</code>
STRING
        );
    }
}
