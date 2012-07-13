<?php

namespace PHPSpec2\Exception\Example;

class ErrorException extends ExampleException
{
    private $levels = array(
        E_WARNING           => 'Warning',
        E_NOTICE            => 'Notice',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
    );

    /**
     * Initializes error handler exception.
     *
     * @param string $level   error level
     * @param string $message error message
     * @param string $file    error file
     * @param string $line    error line
     */
    public function __construct($level, $message, $file, $line)
    {
        parent::__construct(sprintf('%s: %s in %s line %d',
            isset($this->levels[$level]) ? $this->levels[$level] : $level,
            $message,
            $file,
            $line
        ));
    }
}
