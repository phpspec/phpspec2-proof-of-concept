<?php

namespace PHPSpec2\Extension;

use PHPSpec2\Console\ExtendableApplicationInterface as Application;
use PHPSpec2\Configuration\Configuration;

interface ExtensionInterface
{
    public function setApplication(Application $application);
    public function setConfiguration(Configuration $configuration);
    public function extend();
}
