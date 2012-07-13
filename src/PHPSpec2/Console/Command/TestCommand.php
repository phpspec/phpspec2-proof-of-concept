<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use ReflectionClass;

use PHPSpec2\Tester;

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
        $tester = new Tester(new EventDispatcher());

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

            $tester->testSpecification($spec);
        }
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
        if (!$reflection->implementsInterface('PHPSpec2\\SpecificationInterface')) {
            return;
        }

        return $reflection;
    }
}
