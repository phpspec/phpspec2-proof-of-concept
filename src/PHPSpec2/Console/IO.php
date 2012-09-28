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

    public function writeln($messages = '')
    {
        $this->write($messages, true);
    }

    public function write($messages = '', $newline = false)
    {
        $this->output->write($messages, $newline);
        $this->lastMessage = join($newline ? "\n" : '', (array) $messages);
    }

    public function overwrite($messages, $newline = true, $size = null)
    {
        // messages can be an array, let's convert it to string anyway
        $messages = join($newline ? "\n" : '', (array) $messages);

        // since overwrite is supposed to overwrite last message...
        if (!isset($size)) {
            // removing possible formatting of lastMessage with strip_tags
            $size = strlen(strip_tags($this->lastMessage));
        }
        // ...let's fill its length with backspaces
        $this->write(str_repeat("\x08", $size), false);

        // write the new message
        $this->write($messages, false);

        $fill = $size - strlen(strip_tags($messages));
        if ($fill > 0) {
            // whitespace whatever has left
            $this->write(str_repeat(' ', $fill), false);
            // move the cursor back
            $this->write(str_repeat("\x08", $fill), false);
        }

        if ($newline) {
            $this->write('');
        }
        $this->lastMessage = $messages;
    }

    public function ask($question, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }

    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }

    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $this->helperSet->get('dialog')->askAndValidate($this->output, $question, $validator, $attempts, $default);
    }
}
