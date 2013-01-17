<?php

namespace PHPSpec2\Formatter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Console\IO;
use PHPSpec2\Formatter\Presenter\ExceptionPresenterInterface;
use PHPSpec2\Listener\StatisticsCollector;

interface FormatterInterface extends EventSubscriberInterface
{
    public function setIO(IO $io);
    public function setExceptionPresenter(ExceptionPresenterInterface $presenter);
    public function setStatisticsCollector(StatisticsCollector $stats);
}
