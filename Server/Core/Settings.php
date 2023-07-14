<?php

namespace Server\Core;

use Server\Core\EnumConst;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}


class Settings
{
    public $settings;

    /**
     * Server constructor.
     */
    function __construct()
    {
        $this->config = new Config;
        $this->settings = $this->config->getConfig('settings')->getObjectResult();
    }

    public function getDomainName() {
        return $this->settings->domain;
    }

    public function getTitle() {
        return $this->settings->siteTitle;
    }

    public function getSiteName() {
        return $this->settings->siteName;
    }

    public function getVersion() {
        return EnumConst::VERSION;
    }
}