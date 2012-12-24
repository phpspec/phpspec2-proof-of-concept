<?php

namespace PHPSpec2\Formatter\Presenter;

use PHPSpec2\Exception\Exception;

Interface ExceptionPresenterInterface
{
    public function presentException(Exception $exception, $verbose = false);

    public function presentExceptionDifference(Exception $exception);

    public function presentExceptionStackTrace(Exception $exception);
}
