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
        return array(
            'afterExample' => 'afterExample'
        );
    }

    public function afterExample(ExampleEvent $event)
    {
        $exception = $event->getException();
        if (null !== $exception && $exception instanceof MethodNotFoundException) {
            if ($this->io->askConfirmation(sprintf(
                "         <info>You want me to create it for you?</info> <value>[Y/n]</value> "
            ))) {
                $class  = new \ReflectionClass($exception->getSubject());
                $method = $exception->getMethod();

                $content = file_get_contents($class->getFileName());
                $content = preg_replace('/}[ \n]*$/', <<<METHOD

    public function $method()
    {
        // TODO: implement
    }
METHOD
                ."\n}\n", $content);

                file_put_contents($class->getFileName(), $content);

                $this->io->writeln(sprintf(
                    "         <info>Method <value>%s::%s</value> has been created.</info>\n",
                    $class->getName(), $method
                ));
            }
        }
    }
}
