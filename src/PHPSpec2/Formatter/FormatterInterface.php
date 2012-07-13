<?php

namespace PHPSpec2\Formatter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Console\IO;

interface FormatterInterface extends EventSubscriberInterface
{
    public function setIO(IO $io);
}
