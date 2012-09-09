<?php

namespace PHPSpec2;

use Symfony\Component\Finder\Finder;

use SplFileInfo;
use ReflectionClass;

class Locator
{
    public function getSpecifications($path)
    {
        $specs = array();
        if (is_dir($path)) {
            $files = Finder::create()->files()->name('*.php')->in($path);
            foreach ($files as $file) {
                if ($fromFile = $this->getSpecificationsFromFile($file)) {
                    $specs = array_merge($specs, $fromFile);
                }
            }
        } elseif (is_file($path)) {
            $file = new SplFileInfo(realpath($path));
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
