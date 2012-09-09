<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

// use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\EventDispatcher\EventDispatcher;

class DescribeCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('describe');

        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::REQUIRED, 'Spec to describe'),
            new InputOption('path', null, InputOption::VALUE_OPTIONAL, 'Specs path', 'spec')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classname = str_replace('/', '\\', $input->getArgument('spec'));
        $filepath  = realpath($input->getOption('path')).DIRECTORY_SEPARATOR
            .str_replace('\\', DIRECTORY_SEPARATOR, $classname).'.php';

        if (file_exists($filepath)) {
            $output->writeln(sprintf("<error>File '%s' already exists.</error>\n",
                $this->relativizePath($filepath)
            ));

            return 1;
        }

        file_put_contents($filepath, $this->getSpecContentFor($input, $classname));

        $output->writeln(sprintf("<info>Specification for %s created in %s.</info>\n",
            $classname, $input->getOption('path')
        ));
    }

    protected function getSpecContentFor(InputInterface $input, $classname)
    {
        $classname = $input->getOption('path').'\\'.$classname;
        $classpath = str_replace('\\', DIRECTORY_SEPARATOR, $classname);

        return strtr(<<<TPL
<?php

namespace %namespace%;

use PHPSpec2\Specification;

class %class% implements Specification
{
    function it_should_exist()
    {
        \$this->object->shouldNotBe(null);
    }
}
TPL
        , array(
            '%class%'     => basename($classpath),
            '%namespace%' => str_replace('/', '\\', dirname($classpath)),
        ));
    }

    private function relativizePath($filepath)
    {
        return str_replace(getcwd().DIRECTORY_SEPARATOR, '', $filepath);
    }
}
