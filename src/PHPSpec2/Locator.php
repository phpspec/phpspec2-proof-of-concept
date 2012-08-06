<?php

namespace PHPSpec2;

use Symfony\Component\Finder\Finder;

use SplFileInfo;
use ReflectionClass;

class Locator
{
    private $path;
    private $root;

    public function __construct($specsPath = null, $specsRoot = 'spec')
    {
        $specsPath = null !== $specsPath ? realpath($specsPath) : null;
        $specsRoot = realpath($specsRoot);

        $this->path = rtrim($specsPath ?: $specsRoot, DIRECTORY_SEPARATOR);
        $this->root = rtrim($specsRoot, DIRECTORY_SEPARATOR);
    }

    public function getSpecifications()
    {
        $specs = array();
        if (is_dir($this->path)) {
            $files = Finder::create()->files()->name('*.php')->in($this->path);
            foreach ($files as $file) {
                if ($spec = $this->getSpecificationFromFile($file)) {
                    $specs[] = $spec;
                }
            }
        } elseif (is_file($this->path)) {
            $file = new SplFileInfo(realpath($this->path));
            if ($spec = $this->getSpecificationFromFile($file)) {
                $specs[] = $spec;
            }
        }

        return $specs;
    }

    public function getSpecificationFromFile(SplFileInfo $file)
    {
        $filename  = realpath($file->getPathname());
        $classname = str_replace(DIRECTORY_SEPARATOR, '\\',
            str_replace(
                dirname($this->root).DIRECTORY_SEPARATOR, '',
                str_replace('.php', '', $filename)
            )
        );

        if (!class_exists($classname)) {
            return;
        }

        $reflection = new ReflectionClass($classname);
        if (!$reflection->implementsInterface('PHPSpec2\\Specification')) {
            return;
        }

        return $reflection;
    }
}
