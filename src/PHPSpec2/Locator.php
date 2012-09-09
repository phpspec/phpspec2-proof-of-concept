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
                if ($fromFile = $this->getSpecificationsFromFile($file)) {
                    $specs = array_merge($specs, $fromFile);
                }
            }
        } elseif (is_file($this->path)) {
            $file = new SplFileInfo(realpath($this->path));
            if ($fromFile = $this->getSpecificationsFromFile($file)) {
                $specs = array_merge($specs, $fromFile);
            }
        }

        return $specs;
    }

    public function getSpecificationsFromFile(SplFileInfo $file)
    {
        $filename = realpath($file->getPathname());
        $oldClassnames = get_declared_classes();
        require_once $filename;
        $newClassnames = array_diff(get_declared_classes(), $oldClassnames);

        $specs = array();
        foreach ($newClassnames as $classname) {
            $reflection = new ReflectionClass($classname);

            if ($reflection->implementsInterface('PHPSpec2\\Specification')) {
                $specs[] = $reflection;
            }
        }

        return $specs;
    }
}
