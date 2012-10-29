<?php

namespace PHPSpec2\Configuration;

class Configuration
{
    private $config = array();
    private $defaults = array(
        'format' => 'progress'
    );

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getParameter($name)
    {
        if (isset($this->config['default'][$name])) {
            return $this->config['default'][$name];
        } elseif (isset($this->defaults[$name])) {
            return $this->defaults[$name];
        }
        throw new ConfigurationException("$name not configured");
    }

    public function hasExtensions()
    {
        return isset($this->config['default']) &&
               is_array($this->config['default']) &&
               array_key_exists('extensions', $this->config['default']) &&
               !empty($this->config['default']['extensions']);
    }
}