<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use PHPSpec2\Console;

class DescribeCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('desc');

        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::REQUIRED, 'Spec to describe'),
            new InputOption('src-path', null, InputOption::VALUE_REQUIRED, 'Source path', 'src'),
            new InputOption('spec-path', null, InputOption::VALUE_REQUIRED, 'Specs path', 'spec'),
            new InputOption('namespace', null, InputOption::VALUE_REQUIRED, 'Specs NS', 'spec\\'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setFormatter(new Console\Formatter($output->isDecorated()));

        $this->io = new Console\IO($input, $output, $this->getHelperSet());
        $spec     = $input->getArgument('spec');

        if (!is_dir($specsPath = $input->getOption('spec-path'))) {
            mkdir($specsPath, 0777, true);
        }

        if ($srcPath = $input->getOption('src-path')) {
            $spec = preg_replace('#^'.preg_quote($srcPath, '#').'/#', '', $spec);
        }
        $spec = preg_replace('#\.php$#', '', $spec);
        $spec = str_replace('/', '\\', $spec);

        $specsPath = realpath($specsPath).DIRECTORY_SEPARATOR;
        $subject   = str_replace('/', '\\', $spec);
        $classname = $input->getOption('namespace').$subject;
        $filepath  = $specsPath.str_replace('\\', DIRECTORY_SEPARATOR, $spec).'.php';
        $namespace = str_replace('/', '\\', dirname(str_replace('\\', DIRECTORY_SEPARATOR, $classname)));
        $class     = basename(str_replace('\\', DIRECTORY_SEPARATOR, $classname));

        if (file_exists($filepath)) {
            $overwrite = $this->io->askConfirmation(sprintf(
                'File "%s" already exists. Overwrite?', basename($filepath)
            ), false);

            if (!$overwrite) {
                return 1;
            }

            $this->io->writeln();
        }

        $dirpath = dirname($filepath);
        if (!is_dir($dirpath)) {
            mkdir($dirpath, 0777, true);
        }

        file_put_contents($filepath, $this->getSpecContentFor(array(
            '%classname%' => $classname,
            '%namespace%' => $namespace,
            '%filepath%'  => $filepath,
            '%class%'     => $class,
            '%subject%'   => $subject
        )));

        $output->writeln(sprintf("<info>Specification for <value>%s</value> created in <value>%s</value>.</info>\n",
            $subject, $this->relativizePath($filepath)
        ));
    }

    protected function getSpecContentFor(array $parameters)
    {
        $template = file_get_contents(__DIR__.'/../../Resources/templates/spec.php');

        return strtr($template, $parameters);
    }

    private function relativizePath($filepath)
    {
        return str_replace(getcwd().DIRECTORY_SEPARATOR, '', $filepath);
    }
}
