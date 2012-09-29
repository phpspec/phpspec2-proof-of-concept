<?php

namespace PHPSpec2\Formatter\Diff;

interface EngineInterface
{
    public function supports($expected, $actual);
    public function compare($expected, $actual);
}
