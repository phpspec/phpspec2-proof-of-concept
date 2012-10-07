<?php

namespace PHPSpec2\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Console\IO;
use PHPSpec2\Exception\MethodNotFoundException;

class MethodNotFoundListener implements EventSubscriberInterface
{
    private $io;

    public function __construct(IO $io)
    {
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return array('afterExample' => 'afterExample');
    }

    public function afterExample(ExampleEvent $event)
    {
        $exception = $event->getException();
        if (null !== $exception && $exception instanceof MethodNotFoundException) {
            if (null === $ioTemp = $this->io->cutTemp()) {
                if ("\n" !== $this->io->getLastWrittenMessage()) {
                    $this->io->writeln();
                }
            }

            if ($this->io->askConfirmation('Do you want me to create this method for you?')) {
                $class  = new \ReflectionClass($exception->getSubject());
                $method = $exception->getMethod();

                $content = file_get_contents($class->getFileName());
                $content = preg_replace(
                    '/}[ \n]*$/', $this->getMethodContentFor($method) ."\n}\n", $content
                );

                file_put_contents($class->getFileName(), $content);

                $this->io->writeln(sprintf(
                    "\n<info>Method <value>%s::%s()</value> has been created.</info>",
                    $class->getName(), $method
                ), 6);
            }

            $this->io->writeln();
            if (null !== $ioTemp) {
                $this->io->writeTemp($ioTemp);
            }
        }
    }

    protected function getMethodContentFor($method)
    {
        $template = file_get_contents(__DIR__.'/../Resources/templates/method.php');

        return rtrim(strtr($template, array('%method%' => $method)));
    }
}
