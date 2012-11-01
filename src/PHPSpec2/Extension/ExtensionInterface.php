<?php

namespace PHPSpec2\Extension;

use PHPSpec2\ServiceContainer;

interface ExtensionInterface
{
    public function initialize(ServiceContainer $container, array $config);
}
