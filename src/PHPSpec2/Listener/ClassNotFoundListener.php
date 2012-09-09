<?php

namespace PHPSpec2\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Console\IO;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPSpec2\Exception\Stub\ClassDoesNotExistsException;

class ClassNotFoundListener implements EventSubscriberInterface
{
    private $io;
    private $path;

    public function __construct(IO $io, $path = 'src')
    {
        $this->io   = $io;
        $this->path = $path;
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
        if (null !== $exception && $exception instanceof ClassDoesNotExistsException) {
            $output = $this->io->getOutput();
            $dialog = new DialogHelper;

            if ($dialog->askConfirmation($output, sprintf(
                "         <info>You want me to create it for you?</info> <value>[Y/n]</value> "
            ))) {
                $classname = $exception->getClassname();
                $filepath  = $this->path.DIRECTORY_SEPARATOR.
                    str_replace('\\', DIRECTORY_SEPARATOR, $classname).'.php';

                $path = dirname($filepath);
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                file_put_contents($filepath, $this->getClassContentFor($classname));

                $output->writeln(sprintf(
                    "         <info>Class <value>%s</value> has been created.</info>\n",
                    $classname
                ));
            }
        }
    }

    protected function getClassContentFor($classname)
    {
        $classpath = str_replace('\\', DIRECTORY_SEPARATOR, $classname);

        if ('.' === $namespace = dirname($classpath)) {
            return strtr(<<<TPL
<?php

namespace %namespace%;

class %class%
{
}
TPL
            , array(
                '%class%'     => basename($classpath),
                '%namespace%' => str_replace(DIRECTORY_SEPARATOR, '\\', $namespace),
            ));
        } else {
            return strtr(<<<TPL
<?php

class %class%
{
}
TPL
            , array(
                '%class%' => basename($classpath),
            ));
        }
    }
}
