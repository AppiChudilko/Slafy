<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Template
 */
class Template
{
    protected $path;

    protected $template;

    protected $vars = [];

    protected $config;

    /**
     * Template constructor.
     * @param string $path
     */
    public function __construct($path = '') {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . $path; //REQUEST_URI
        $this->config = new Config;
        $this->config = $this->config->getAppiAllConfig()->getObjectResult();
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public function __get($name) {
        if (isset($this->vars[$name])) return $this->vars[$name];
        return '';
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * @param $dir
     * @param bool $strip
     * @return string
     */
    public function display($dir, $strip = true) {

        $this->template = $this->path . $dir;
        if (!file_exists($this->template)) die('Template ' . $this->template . ' does not exist');

        $output = file_get_contents($this->template, true);
        $output = ($strip) ? $this->replaceReg($output) : $output;

        ob_start();
        include($this->path . $dir);
        return ob_get_clean();
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function replaceReg($data) {
        $lit = ["\\t", "\\n", "\\n\\r", "\\r\\n", "  "];
        $sp = ['', '', '', '', ''];
        return str_replace($lit, $sp, $data);
    }

    /**
     * @param mixed $config
     * @return Template
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }
}