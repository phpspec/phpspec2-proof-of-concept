<?php

namespace spec\PHPSpec2\Diff;

use PHPSpec2\Specification;

class Diff implements Specification
{
    /**
     * @param Prophet $engine1 mock of PHPSpec2\Diff\EngineInterface
     * @param Prophet $engine2 mock of PHPSpec2\Diff\EngineInterface
     */
    function it_should_choose_proper_engine($engine1, $engine2)
    {
        $engine1->supports('string1', 'string2')->willReturn(true);
        $engine2->supports('string1', 'string2')->willReturn(false);
        $engine1->compare('string1', 'string2')->willReturn('string1 !== string2');

        $engine1->supports(2, 1)->willReturn(false);
        $engine2->supports(2, 1)->willReturn(true);
        $engine2->compare(2, 1)->willReturn('2 !== 1');

        $this->diff->addEngine($engine1);
        $this->diff->addEngine($engine2);

        $this->diff->compare('string1', 'string2')->shouldReturn('string1 !== string2');
        $this->diff->compare(2, 1)->shouldReturn('2 !== 1');
    }

    function it_should_return_null_if_engine_not_found()
    {
        $this->diff->compare(1, 2)->shouldReturn(null);
    }
}
