<?php

namespace PHPSpec2\Diff;

class Diff
{
    private $engines = array();

    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }

    public function compare($expected, $actual)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($expected, $actual)) {
                return trim($engine->compare($expected, $actual));
            }
        }

        return null;
    }
}
