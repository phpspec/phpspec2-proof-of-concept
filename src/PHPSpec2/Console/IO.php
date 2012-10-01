<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

class IO
{
    private $input;
    private $output;
    private $helpers;
    private $lastMessage;

    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helpers)
    {
        $this->input   = $input;
        $this->output  = $output;
        $this->helpers = $helpers;
    }

    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    public function isVerbose()
    {
        return (bool) $this->input->getOption('verbose');
    }

    public function writeln($message = '')
    {
        $this->write($message, true);
    }

    public function write($message, $newline = false)
    {
        $this->output->write($message, $newline);
        $this->lastMessage = $message.($newline ? "\n" : '');
    }

    public function overwriteln($message = '')
    {
        $this->overwrite($message, true);
    }

    public function overwrite($message, $newline = false)
    {
        $size = strlen(strip_tags($this->lastMessage));

        $this->write(str_repeat("\x08", $size));
        $this->write($message);

        $fill = $size - strlen(strip_tags($message));
        if ($fill > 0) {
            $this->write(str_repeat(' ', $fill));
            $this->write(str_repeat("\x08", $fill));
        }

        if ($newline) {
            $this->writeln();
        }

        $this->lastMessage = $message.($newline ? "\n" : '');
    }

    public function ask($question, $default = null)
    {
        return $this->helpers->get('dialog')->ask($this->output, $question, $default);
    }

    public function askConfirmation($question, $default = true)
    {
        return $this->helpers->get('dialog')->askConfirmation($this->output, $question, $default);
    }

    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $this->helpers->get('dialog')->askAndValidate($this->output, $question, $validator, $attempts, $default);
    }
}
