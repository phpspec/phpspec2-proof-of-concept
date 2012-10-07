<?php

namespace PHPSpec2\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Console\IO;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPSpec2\Exception\ClassNotFoundException;

class ClassNotFoundListener implements EventSubscriberInterface
{
    private $io;
    private $path;

    public function __construct(IO $io, $path = 'src')
    {
        $this->io   = $io;
        $this->path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public static function getSubscribedEvents()
    {
        return array('afterExample' => 'afterExample');
    }

    public function afterExample(ExampleEvent $event)
    {
        $exception = $event->getException();
        if (null !== $exception && $exception instanceof ClassNotFoundException) {
            if (null === $ioTemp = $this->io->cutTemp()) {
                if ("\n" !== $this->io->getLastWrittenMessage()) {
                    $this->io->writeln();
                }
            }

            if ($this->io->askConfirmation('You want me to create it for you?')) {
                $classname = $exception->getClassname();
                $filepath  = $this->path.str_replace('\\', DIRECTORY_SEPARATOR, $classname).'.php';

                $path = dirname($filepath);
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                file_put_contents($filepath, $this->getClassContentFor($classname));

                $this->io->writeln(sprintf(
                    "\n<info>class <value>%s</value> has been created.</info>", $classname
                ), 6);
            }

            $this->io->writeln();
            if (null !== $ioTemp) {
                $this->io->writeTemp($ioTemp);
            }
        }
    }

    protected function getClassContentFor($classname)
    {
        $classpath = str_replace('\\', DIRECTORY_SEPARATOR, $classname);

        if ('.' !== $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', dirname($classpath))) {
            $template = file_get_contents(__DIR__.'/../Resources/templates/nsclass.php');

            return strtr($template, array(
                '%classname%' => $classname,
                '%namespace%' => $namespace,
                '%class%'     => basename($classpath),
            ));
        }

        $template = file_get_contents(__DIR__.'/../Resources/templates/class.php');

        return strtr($template, array(
            '%classname%' => $classname,
            '%class%'     => basename($classpath),
        ));
    }
}
