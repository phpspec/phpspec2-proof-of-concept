<?php

namespace PHPSpec2\Formatter\Diff;

require_once __DIR__.'/PhpDiff.php';

class StringDiff
{
    public static function diff($a, $b)
    {
        $a = is_array($a) ? $a : explode("\n", $a);
        $b = is_array($b) ? $b : explode("\n", $b);

        $diff = new \Diff($a, $b, array());

        $renderer = new \Diff_Renderer_Text_Unified;
        $text = $diff->render($renderer);

        $lines = array();
        foreach (explode("\n", $text) as $line) {
            if (0 === strpos($line, '-')) {
                $lines[] = sprintf('<diff-del>%s</diff-del>', $line);
            } elseif (0 === strpos($line, '+')) {
                $lines[] = sprintf('<diff-add>%s</diff-add>', $line);
            } else {
                $lines[] = $line;
            }
        }

        return sprintf('<code>%s</code>', implode("\n", $lines));
    }
}
