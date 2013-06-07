<?php

namespace PHPSpec2\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Console\IO;
use PHPSpec2\Exception\FactoryMethodNotFoundException;

class FactoryMethodNotFoundListener implements EventSubscriberInterface
{
    private $io;
    private $proposedMethods = array();

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
        if (null !== $exception && $exception instanceof FactoryMethodNotFoundException) {
            if (null === $ioTemp = $this->io->cutTemp()) {
                if ("\n" !== $this->io->getLastWrittenMessage()) {
                    $this->io->writeln();
                }
            }
            $shortcut = $exception->getSubject().'::'.$exception->getMethod();
            if (in_array($shortcut, $this->proposedMethods)) {
                return;
            }
            $this->proposedMethods[] = $shortcut;

            if ($this->io->askConfirmation('Do you want me to create this factory method for you?')) {
                $class  = new \ReflectionClass($exception->getSubject());
                $method = $exception->getMethod();

                $content = file_get_contents($class->getFileName());
                $content = preg_replace(
                    '/}[ \n]*$/', $this->getMethodContentFor($method) ."\n}\n", $content
                );

                file_put_contents($class->getFileName(), $content);

                $this->io->writeln(sprintf(
                        "\n<info>Factory Method <value>%s::%s()</value> has been created.</info>",
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
        $template = file_get_contents(__DIR__.'/../Resources/templates/factorymethod.php');

        return rtrim(strtr($template, array('%method%' => $method)));
    }
}
