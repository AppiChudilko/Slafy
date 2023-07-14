<?php
namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Init
 */
class Init
{
    public $config;

    public function initAppi() {
        $this->initCfg();
        $this->initRequest();

        session_start();

        if (!isset($_COOKIE['UTC'])) {
            setcookie("UTC", 0, 0x6FFFFFFF, "/");
        }

        return $this;
    }

    protected function initCfg() {
        $this->config = new Config;
        $this->config = $this->config->getAppiAllConfig()->getObjectResult();

        if ($this->config->displayErrors) {
            @error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
            @ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
            @ini_set ( 'display_errors', true );
            @ini_set ( 'html_errors', false );
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }
        if ($this->config->isWhiteIp) {
            if($_SERVER['REMOTE_ADDR'] != $this->config->whiteIp) {
                header('Location: https://appi-rp.com/');
                die("<h1><center>appi-rp.com</center></h1>");
            }
        }

        return $this;
    }

    protected function initRequest() {
        return $this;
    }
}