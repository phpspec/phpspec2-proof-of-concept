<?php

namespace PHPSpec2\Configuration;

class Configuration
{
    private $profile;
    private $config = array();
    private $defaults = array(
        'format' => 'progress'
    );

    public function __construct($config, $profile = 'default')
    {
        $this->config = $config;
        $this->profile = $profile;
    }

    public function getParameter($name)
    {
        if (isset($this->config[$this->profile][$name])) {
            return $this->config[$this->profile][$name];
        } elseif (isset($this->defaults[$name])) {
            return $this->defaults[$name];
        }
        throw new ConfigurationException("$name not configured");
    }

    public function hasExtensions()
    {
        return isset($this->config['default']) &&
               is_array($this->config['default']) &&
               array_key_exists('extensions', $this->config['default']);
    }
}