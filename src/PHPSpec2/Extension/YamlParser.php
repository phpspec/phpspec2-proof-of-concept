<?php

namespace PHPSpec2\Extension;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public function parse($file)
    {
        return Yaml::parse($file);
    }
}
