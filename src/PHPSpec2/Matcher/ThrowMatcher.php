<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Looper\Looper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class ThrowMatcher implements MatcherInterface
{
    private $unwrapper;
    private $presenter;

    public function __construct(ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter)
    {
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
    }

    public function supports($name, $subject, array $arguments)
    {
        return 'throw' === $name;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        return $this->getLooper(array($this, 'verifyPositive'), $subject, $arguments);
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        return $this->getLooper(array($this, 'verifyNegative'), $subject, $arguments);
    }

    public function verifyPositive($callable, array $arguments, $class = null, $message = null)
    {
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            if (null === $class) {
                return;
            }

            if (!$e instanceof $class) {
                throw new FailureException(sprintf(
                    'Expected exception of class %s, but got %s.',
                    $this->presenter->presentString($class),
                    $this->presenter->presentValue($e)
                ));
            }

            if (null !== $message && $e->getMessage() !== $message) {
                throw new NotEqualException(sprintf(
                    'Expected exception message %s, but got %s.',
                    $this->presenter->presentValue($message),
                    $this->presenter->presentValue($e->getMessage())
                ), $message, $e->getMessage());
            }

            return;
        }

        throw new FailureException('Expected to get exception, none got.');
    }

    public function verifyNegative($callable, array $arguments, $class = null, $message = null)
    {
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            if (null === $class) {
                throw new FailureException(sprintf(
                    'Expected to not throw any exceptions, but got %s.',
                    $this->presenter->presentValue($e)
                ));
            }

            if ($e instanceof $class && null === $message) {
                throw new FailureException(sprintf(
                    'Expected to not throw %s exception, but got it.',
                    $this->presenter->presentString($class)
                ));
            }

            if ($e instanceof $class && $e->getMessage() === $message) {
                throw new FailureException(sprintf(
                    "Expected to not throw %s exception\n".
                    "with %s message,\nbut got it.",
                    $this->presenter->presentString($class),
                    $this->presenter->presentValue($message)
                ));
            }
        }
    }

    private function getLooper($check, $subject, array $arguments)
    {
        list($class, $message) = $this->getExceptionInformation($arguments);
        $unwrapper = $this->unwrapper;

        return new Looper(
            function($method, $arguments) use($check, $subject, $class, $message, $unwrapper) {
                $arguments = $unwrapper->unwrapAll($arguments);

                if (preg_match('/^during(.+)$/', $method, $matches)) {
                    $callable = lcfirst($matches[1]);
                } elseif (isset($arguments[0])) {
                    if (strpos($method, 'during') === false) {
                        throw new MatcherException('Incorrect usage of matcher Throw, either prefix the method with "during" and capitalize the first character of the method or use ->during(\'callable\', array(arguments)).' .PHP_EOL. 'E.g.'.PHP_EOL.'->during' . ucfirst($method) . '(arguments)'.PHP_EOL.'or'.PHP_EOL.'->during(\'' . $method . '\', array(arguments))');
                    }
                    $callable  = $arguments[0];
                    $arguments = isset($arguments[1]) ? $arguments[1] : array();
                } else {
                    throw new MatcherException('Provide callable to be checked for throwing.');
                }

                $callable = is_string($callable) ? array($subject, $callable) : $callable;

                return call_user_func($check, $callable, $arguments, $class, $message);
            }
        );
    }

    private function getExceptionInformation(array $arguments)
    {
        if (0 == count($arguments)) {
            return array(null, null);
        }

        if (is_string($arguments[0])) {
            return array($arguments[0], isset($arguments[1]) ? $arguments[1] : null);
        }

        if (is_object($arguments[0]) && $arguments[0] instanceof \Exception) {
            return array(get_class($arguments[0]), $arguments[0]->getMessage());
        }

        throw new MatcherException(sprintf(
            "Wrong argument provided in throw matcher.\n".
            "Fully qualified classname or exception instance expected,\n".
            "Got %s.",
            $this->presenter->presentValue($arguments[0])
        ));
    }

    public function getPriority()
    {
        return 1;
    }
}
