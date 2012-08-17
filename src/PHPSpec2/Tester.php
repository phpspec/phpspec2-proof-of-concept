<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionClass;
use ReflectionMethod;

use Mockery;

use PHPSpec2\Stub\ObjectStub;
use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;
use PHPSpec2\Stub\LazyInstance;

class Tester
{
    const RUN_ALL = '.*';
    private $eventDispatcher;
    private $matchers = array();
    private $runOnly = Tester::RUN_ALL;
    private $failFast;
    private $wasAborted = false;

    public function __construct(EventDispatcherInterface $dispatcher, MatchersCollection $matchers, array $options = array())
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
        $this->runOnly         = isset($options['example']) ? $options['example'] : Tester::RUN_ALL;
        $this->failFast        = isset($options['fail-fast']) ? $options['fail-fast'] : false;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function testSpecification(ReflectionClass $spec)
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
                $result = max($result, $this->testExample($example));

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

    public function testExample(ReflectionMethod $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));

        $spec    = $example->getDeclaringClass();
        $subject = null;
        if (class_exists($class = preg_replace(array("|^spec\\\|", "|Spec$|"), '', $spec->getName()))) {
            $subject = new LazyInstance($class);
        }

        $instance = $spec->newInstance();
        $instance->object = new ObjectStub($subject, $this->matchers);
        $stubs = $this->getStubsForExample($instance, $example);

        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        try {
            $this->callMethodWithStubs($instance, $example, $stubs);
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

    protected function getStubsForExample(Specification $instance, ReflectionMethod $example)
    {
        $stubs = array();
        if (method_exists($instance, 'described_with')) {
            $descriptor = new ReflectionMethod($instance, 'described_with');
            $stubs = $this->mergeStubsFromMethod($stubs, $descriptor);
            $this->callMethodWithStubs($instance, $descriptor, $stubs);
        }

        return $this->mergeStubsFromMethod($stubs, $example);
    }

    protected function callMethodWithStubs(Specification $instance, ReflectionMethod $method, array $stubs)
    {
        $arguments = array();
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $stubs[$parameter->getName()];
        }

        $method->invokeArgs($instance, $arguments);
    }

    private function mergeStubsFromMethod(array $stubs, ReflectionMethod $method)
    {
        $stubs = $this->mergeStubsFromDocComment($stubs, $method->getDocComment());

        foreach ($method->getParameters() as $parameter) {
            if (!isset($stubs[$parameter->getName()])) {
                $stubs[$parameter->getName()] = new ObjectStub(null, $this->matchers);
            }
        }

        return $stubs;
    }

    private function mergeStubsFromDocComment(array $stubs, $comment)
    {
        if (false === $comment || '' == trim($comment)) {
            return $stubs;
        }

        foreach (explode("\n", $comment) as $line) {
            $line = preg_replace('/^\/\*\*\s*|^\s*\*\s*|\s*\*\/$|\s*$/', '', $line);

            if (preg_match('#^@param(?: *[^ ]*)? *\$([^ ]*) *mock of (.*)$#', $line, $match)) {
                if (!isset($stubs[$match[1]])) {
                    $stubs[$match[1]] = new ObjectStub(null, $this->matchers);
                    $stubs[$match[1]]->is_a_mock_of($match[2]);
                }
            }
        }

        return $stubs;
    }

    private function isExampleTestable(ReflectionMethod $example)
    {
        return 'described_with' !== $example->getName();
    }

    private function specContainsFilteredExamples(array $examples)
    {
        if (self::RUN_ALL !== $this->runOnly) {
            foreach ($examples as $example) {
                if ($this->exampleIsFiltered($example)) {
                    return true;
                }
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
