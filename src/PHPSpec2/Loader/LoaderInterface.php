<?php

namespace PHPSpec2\Loader;

interface LoaderInterface
{
    public function loadFromFile($filename, $line = null);
}
