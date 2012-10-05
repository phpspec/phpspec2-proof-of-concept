<?php

namespace PHPSpec2\Formatter\Presenter;

class TaggedPresenter extends StringPresenter
{
    public function presentString($string)
    {
        return '<value>'.parent::presentString($string).'</value>';
    }

    public function presentCodeLine($number, $line)
    {
        return sprintf('<lineno>%s</lineno> <code>%s</code>', $number, $line);
    }

    public function presentHighlight($line)
    {
        return '<hl>'.$line.'</hl>';
    }
}
