<?php

namespace PHPSpec2\Formatter\Presenter;

class TaggedPresenter extends StringPresenter
{
    public function presentString($string)
    {
        return '<value>'.parent::presentString($string).'</value>';
    }
}
