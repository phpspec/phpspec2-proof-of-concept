<?php

namespace spec\PHPSpec2\Formatter\Presenter\Differ;

use PHPSpec2\ObjectBehavior;

class Differ extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\Differ\EngineInterface $engine1
     * @param PHPSpec2\Formatter\Presenter\Differ\EngineInterface $engine2
     */
    function it_should_choose_proper_engine($engine1, $engine2)
    {
        $engine1->supports('string1', 'string2')->willReturn(true);
        $engine2->supports('string1', 'string2')->willReturn(false);
        $engine1->compare('string1', 'string2')->willReturn('string1 !== string2');

        $engine1->supports(2, 1)->willReturn(false);
        $engine2->supports(2, 1)->willReturn(true);
        $engine2->compare(2, 1)->willReturn('2 !== 1');

        $this->addEngine($engine1);
        $this->addEngine($engine2);

        $this->compare('string1', 'string2')->shouldReturn('string1 !== string2');
        $this->compare(2, 1)->shouldReturn('2 !== 1');
    }

    function it_should_return_null_if_engine_not_found()
    {
        $this->compare(1, 2)->shouldReturn(null);
    }
}
