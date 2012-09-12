<?php

namespace PHPSpec2;

use Symfony\Component\Finder\Finder;

use SplFileInfo;
use PHPSpec2\Loader\LoaderInterface;

class Locator
{
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getSpecifications($path)
    {
        $line = null;
        if (preg_match('/^(.*)\:(\d+)$/', $path, $matches)) {
            list($_, $path, $line) = $matches;
        }

        $specs = array();
        if (is_dir($path)) {
            $files = Finder::create()->files()->name('*.php')->in($path);
            foreach ($files as $file) {
                if ($fromFile = $this->getSpecificationsFromFile($file, $line)) {
                    $specs = array_merge($specs, $fromFile);
                }
            }
        } elseif (is_file($path)) {
            $file = new SplFileInfo(realpath($path));
            if ($fromFile = $this->getSpecificationsFromFile($file, $line)) {
                $specs = array_merge($specs, $fromFile);
            }
        }

        return $specs;
    }

    public function getSpecificationsFromFile(SplFileInfo $file, $line = null)
    {
        return $this->loader->loadFromFile($file->getPathname(), $line);
    }
}
