<?php

namespace PHPSpec2\Extension;

use PHPSpec2\ServiceContainer;

class Configuration
{
    private $container;

    public function __construct(ServiceContainer $container, YamlParser $parser = null)
    {
        $this->container = $container;
        $this->parser    = $parser ?: new YamlParser;
    }

    public function read($file)
    {
        if ($config = $this->parser->parse($file)) {
            foreach ($config as $key => $val) {
                $this->container->set($key, $val);
            }
        }
    }
}
