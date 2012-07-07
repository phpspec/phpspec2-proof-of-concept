<?php

namespace PHPSpec\PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use ReflectionClass;
use ReflectionMethod;

use PHPSpec\PHPSpec2\Stub;
use PHPSpec\PHPSpec2\Matcher;
use PHPSpec\PHPSpec2\SpecificationInterface;

class TestCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('test');

        $this->setDefinition(array());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $specsPath = realpath('specs');

        $finder = Finder::create();
        $files  = $finder
            ->files()
            ->name('*.php')
            ->in($specsPath)
        ;

        foreach ($files as $file) {
            if (!$spec = $this->getSpecReflectionFromFile($file, $specsPath)) {
                continue;
            }

            foreach ($spec->getMethods(ReflectionMethod::IS_PUBLIC) as $specMethod) {
                $specInstance = $spec->newInstance();
                $stubs = array();
                foreach (array('describedWith', 'described_with') as $describer) {
                    // describing methods are not specs
                    if ($describer === $specMethod->getName()) {
                        continue 2;
                    }

                    // call describing method
                    if ($spec->hasMethod($describer)) {
                        $method = $spec->getMethod($describer);
                        $stubs  = $this->getStubsForMethod($method, $stubs);
                        $this->callMethodWithStubs($specInstance, $method, $stubs);
                    }
                }

                $stubs = $this->getStubsForMethod($specMethod, $stubs);
                $this->callMethodWithStubs($specInstance, $specMethod, $stubs);
            }
        }
    }

    private function createNewStub($subject = null)
    {
        $stub = new Stub($subject);
        $stub->registerMatcher(new Matcher\ShouldReturnMatcher);
        $stub->registerMatcher(new Matcher\ShouldContainMatcher);

        return $stub;
    }

    private function getStubsForMethod(ReflectionMethod $method, array $stubs)
    {
        foreach ($method->getParameters() as $parameter) {
            if (!isset($stubs[$parameter->getName()])) {
                $stubs[$parameter->getName()] = $this->createNewStub();
            }
        }

        return $stubs;
    }

    private function callMethodWithStubs(SpecificationInterface $spec, ReflectionMethod $method, array $stubs)
    {
        $arguments = array();
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $stubs[$parameter->getName()];
        }

        $method->invokeArgs($spec, $arguments);
    }

    private function getSpecReflectionFromFile(SplFileInfo $file, $specsPath)
    {
        $filename  = realpath($file->getPathname());
        $classname = str_replace(DIRECTORY_SEPARATOR, '\\',
            str_replace(
                $specsPath.DIRECTORY_SEPARATOR, '',
                str_replace('.php', '', $filename)
            )
        );

        if (!class_exists($classname)) {
            return;
        }

        $reflection = new ReflectionClass($classname);
        if (!$reflection->implementsInterface('PHPSpec\\PHPSpec2\\SpecificationInterface')) {
            return;
        }

        return $reflection;
    }
}
