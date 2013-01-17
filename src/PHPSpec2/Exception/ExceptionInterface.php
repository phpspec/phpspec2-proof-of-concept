<?php

namespace PHPSpec2\Exception;

Interface ExceptionInterface
{
    public function getCause();
    public function setCause($cause);
}
