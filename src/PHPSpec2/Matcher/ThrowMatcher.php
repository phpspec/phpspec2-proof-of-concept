<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Looper\Looper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Factory\ReflectionFactory;

class ThrowMatcher implements MatcherInterface
{
    private $unwrapper;
    private $presenter;

    public function __construct(ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter, ReflectionFactory $factory = null)
    {
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
        $this->factory   = $factory ?: new ReflectionFactory;
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

    public function verifyPositive($callable, array $arguments, $exception = null)
    {
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            if (null === $exception) {
                return;
            }

            if (!$e instanceof $exception) {
                throw new FailureException(sprintf(
                    'Expected exception of class %s, but got %s.',
                    $this->presenter->presentValue($exception),
                    $this->presenter->presentValue($e)
                ));
            }

            if (is_object($exception)) {
                $exceptionRefl = $this->factory->create($exception);
                foreach ($exceptionRefl->getProperties() as $property) {
                    if (in_array($property->getName(), array('file', 'line'))) {
                        continue;
                    }
                    $property->setAccessible(true);

                    if (null !== $property->getValue($exception) && $property->getValue($e) !== $property->getValue($exception)) {
                        throw new NotEqualException(sprintf(
                            'Expected exception %s %s, but got %s.',
                            $property->getName(),
                            $this->presenter->presentValue($property->getValue($exception)),
                            $this->presenter->presentValue($property->getValue($e))
                        ), $property->getValue($exception), $property->getValue($e));
                    }
                }
            }

            return;
        }

        throw new FailureException('Expected to get exception, none got.');
    }

    public function verifyNegative($callable, array $arguments, $exception = null)
    {
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            if (null === $exception) {
                throw new FailureException(sprintf(
                    'Expected to not throw any exceptions, but got %s.',
                    $this->presenter->presentValue($e)
                ));
            }

            if ($e instanceof $exception) {
                $invalidProperties = array();
                if (is_object($exception)) {
                    $exceptionRefl = $this->factory->create($exception);
                    foreach ($exceptionRefl->getProperties() as $property) {
                        if (in_array($property->getName(), array('file', 'line'))) {
                            continue;
                        }
                        $property->setAccessible(true);

                        if (null !== $property->getValue($exception) && $property->getValue($e) === $property->getValue($exception)) {
                            $invalidProperties[] = sprintf(
                                '  %s %s',
                                $property->getName(),
                                $this->presenter->presentValue($property->getValue($exception))
                            );
                        }
                    }
                }

                $withProperties = '';
                if (count($invalidProperties) > 0) {
                    $withProperties = sprintf(" with\n%s,\n", implode(",\n", $invalidProperties));
                }

                throw new FailureException(sprintf(
                    'Expected to not throw %s exception%s but got it.',
                    $this->presenter->presentValue(
                        is_object($exception) ? get_class($exception) : $exception
                    ),
                    $withProperties
                ));
            }
        }
    }

    private function getLooper($check, $subject, array $arguments)
    {
        $exception = $this->getException($arguments);
        $unwrapper = $this->unwrapper;

        return new Looper(
            function ($method, $arguments) use($check, $subject, $exception, $unwrapper) {
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

                return call_user_func($check, $callable, $arguments, $exception);
            }
        );
    }

    private function getException(array $arguments)
    {
        if (0 == count($arguments)) {
            return null;
        }

        if (is_string($arguments[0])) {
            return $arguments[0];
        }

        if (is_object($arguments[0]) && $arguments[0] instanceof \Exception) {
            return $arguments[0];
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
