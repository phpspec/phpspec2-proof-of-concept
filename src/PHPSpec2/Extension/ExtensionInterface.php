<?php

namespace PHPSpec2\Extension;

use PHPSpec2\Console;

interface ExtensionInterface
{
    public function __construct(ExtendableApplicationInterface $application);

    public function extend();
}
