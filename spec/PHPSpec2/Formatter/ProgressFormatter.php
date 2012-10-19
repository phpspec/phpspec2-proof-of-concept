<?php

namespace spec\PHPSpec2\Formatter;

use PHPSpec2\ObjectBehavior;

class ProgressFormatter extends ObjectBehavior
{
    /**
     * @param $io PHPSpec2\Console\IO
     * @param $stats PHPSpec2\Listener\StatisticsCollector
     * @param $event PHPSpec2\Event\SuiteEvent
     **/
    function it_prints_example_in_singular_if_count_is_1($io, $stats, $event)
    {
        $event->beAMockOf('PHPSpec2\Event\SuiteEvent');
        $io->beAMockOf('PHPSpec2\Console\IO');
        $stats->beAMockOf('PHPSpec2\Listener\StatisticsCollector');

        $this->setIO($io);
        $this->setStatisticsCollector($stats);
        $stats->getEventsCount()->willReturn(1);
        //$io->writeln("1 examples ")->shouldNotBeCalled();
        $io->writeln("1 example ")->shouldBeCalled();
        $this->afterSuite($event);
    }
}
