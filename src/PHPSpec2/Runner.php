<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionClass;
use ReflectionMethod;

use Mockery;

use PHPSpec2\Prophet\Prophet;
use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;
use PHPSpec2\Prophet\LazyObject;

class Runner
{
    const RUN_ALL = '.*';
    private $eventDispatcher;
    private $matchers = array();
    private $runOnly = Runner::RUN_ALL;
    private $failFast;
    private $wasAborted = false;

    public function __construct(EventDispatcherInterface $dispatcher, MatchersCollection $matchers, array $options = array())
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
        $this->runOnly         = isset($options['example']) ? $options['example'] : Runner::RUN_ALL;
        $this->failFast        = isset($options['fail-fast']) ? $options['fail-fast'] : false;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function runSpecification(ReflectionClass $spec)
    {
        $examples = $spec->getMethods(ReflectionMethod::IS_PUBLIC);

        if (!$this->specContainsFilteredExamples($examples)) {
            return 0;
        }

        $this->eventDispatcher->dispatch('beforeSpecification',
            new SpecificationEvent($spec)
        );

        $result = 0;
        foreach ($examples as $example) {
            if ($this->isExampleTestable($example) &&
                $this->exampleIsFiltered($example)) {
                $result = max($result, $this->runExample($example));

                if ($this->failFast && $result) {
                    $this->wasAborted = true;
                    return $result;
                }
            }
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new SpecificationEvent($spec, $result)
        );

        return $result;
    }

    public function runExample(ReflectionMethod $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));

        $spec     = $example->getDeclaringClass();
        $subject  = null;
        $class    = preg_replace(array("|^spec\\\|", "|Spec$|"), '', $spec->getName());
        $subject  = new LazyObject($class);
        $instance = $spec->newInstance();

        $className = substr($spec->getName(), (int)strrpos($spec->getName(), '\\') + 1);
        $className = strtolower($className[0]) . substr($className, 1);

        $prophet = new Prophet($subject, clone $this->matchers);
        $instance->$className = $instance->object = $prophet;

        $prophets = $this->getProphetsForExample($instance, $example);

        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        try {
            $this->callMethodWithProphets($instance, $example, $prophets);
            Mockery::close();

            $event = new ExampleEvent($example, ExampleEvent::PASSED);
        } catch (PendingException $e) {
            $event = new ExampleEvent($example, ExampleEvent::PENDING, $e);
        } catch (\Exception $e) {
            $event = new ExampleEvent($example, ExampleEvent::FAILED, $e);
        }

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        $this->eventDispatcher->dispatch('afterExample', $event);

        return $event->getResult();
    }

    /**
     * Custom error handler.
     *
     * This method used as custom error handler when step is running.
     *
     * @see set_error_handler()
     *
     * @param integer $level
     * @param string  $message
     * @param string  $file
     * @param integer $line
     *
     * @return Boolean
     *
     * @throws ErrorException
     */
    final public function errorHandler($level, $message, $file, $line)
    {
        if (0 !== error_reporting()) {
            throw new ErrorException($level, $message, $file, $line);
        }

        // error reporting turned off or more likely suppressed with @
        return false;
    }

    public function wasAborted()
    {
        return $this->wasAborted;
    }

    protected function getProphetsForExample(Specification $instance, ReflectionMethod $example)
    {
        $prophets = array();
        if (method_exists($instance, 'described_with')) {
            $descriptor = new ReflectionMethod($instance, 'described_with');
            $prophets = $this->mergeProphetsFromMethod($prophets, $descriptor);
            $this->callMethodWithProphets($instance, $descriptor, $prophets);
        }

        return $this->mergeProphetsFromMethod($prophets, $example);
    }

    protected function callMethodWithProphets(Specification $instance, ReflectionMethod $method, array $prophets)
    {
        $arguments = array();
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $prophets[$parameter->getName()];
        }

        $method->invokeArgs($instance, $arguments);
    }

    private function mergeProphetsFromMethod(array $prophets, ReflectionMethod $method)
    {
        $prophets = $this->mergeProphetsFromDocComment($prophets, $method->getDocComment());

        foreach ($method->getParameters() as $parameter) {
            if (!isset($prophets[$parameter->getName()])) {
                $prophets[$parameter->getName()] = new Prophet(null, clone $this->matchers);
            }
        }

        return $prophets;
    }

    private function mergeProphetsFromDocComment(array $prophets, $comment)
    {
        if (false === $comment || '' == trim($comment)) {
            return $prophets;
        }

        foreach (explode("\n", $comment) as $line) {
            $line = preg_replace('/^\/\*\*\s*|^\s*\*\s*|\s*\*\/$|\s*$/', '', $line);

            if (preg_match('#^@param(?: *[^ ]*)? *\$([^ ]*) *(double|mock|stub|fake|dummy|spy) of (.*)$#', $line, $match)) {
                if (!isset($prophets[$match[1]])) {
                    $prophets[$match[1]] = new Prophet(null, clone $this->matchers);
                    $prophets[$match[1]]->isAMockOf($match[3]);
                }
            }
        }

        return $prophets;
    }

    private function isExampleTestable(ReflectionMethod $example)
    {
        return (bool) preg_match('/^(it_|its_)/', $example->getName());
    }

    private function specContainsFilteredExamples(array $examples)
    {
        if (self::RUN_ALL !== $this->runOnly) {
            return true;
        }

        foreach ($examples as $example) {
            if ($this->exampleIsFiltered($example)) {
                return true;
            }
        }

        return false;
    }

    private function exampleIsFiltered(ReflectionMethod $example)
    {
        return preg_match(
            "/" . $this->runOnly . "/",
            $example->getName()
        ) !== 0;
    }
}
