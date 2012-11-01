<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

interface CustomMatchersProviderInterface
{
    static public function getMatchers();
}
