<?php

namespace PHPSpec2\Loader;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements LoaderInterface
{
    public function loadFromFile($filename)
    {
        $config   = Yaml::parse($filename);
        return $config;
    }
}