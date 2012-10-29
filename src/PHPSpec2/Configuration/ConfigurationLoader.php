<?php

namespace PHPSpec2\Configuration;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    public function loadFromFile($fileName)
    {
        $config = array();
        if (file_exists($fileName)) {
            $config = Yaml::parse($fileName);
        }
        return new Configuration($config);
    }
}