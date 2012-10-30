<?php

namespace spec\PHPSpec2\Formatter;

use PHPSpec2\ObjectBehavior;

class ProgressFormatter extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Console\IO $io
     * @param PHPSpec2\Listener\StatisticsCollector $stats
     * @param PHPSpec2\Event\SuiteEvent $event
     **/
    function it_prints_example_in_singular_if_count_is_1($io, $stats, $event)
    {
        $event->getTime()->willReturn(42);
        $stats->getCountsHash()->willReturn(array('passed' => 1));
        $stats->getEventsCount()->willReturn(1);
        $io->writeln()->shouldBeCalled();
        $io->writeln("\n42000ms")->shouldBeCalled();
        $io->write("(1 passed)")->shouldBeCalled();
        $this->setIO($io);
        $this->setStatisticsCollector($stats);

        $io->write("\n1 example ")->shouldBeCalled();
        $this->afterSuite($event);
    }
}
