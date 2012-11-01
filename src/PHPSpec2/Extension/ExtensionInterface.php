<?php

namespace PHPSpec2\Extension;

use PHPSpec2\Console;

interface ExtensionInterface
{
    public function initialize(ExtendableApplicationInterface $application);
}
