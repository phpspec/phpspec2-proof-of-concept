<?php

namespace PHPSpec2\Diff;

interface EngineInterface
{
    public function supports($expected, $actual);
    public function compare($expected, $actual);
}
