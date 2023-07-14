<?php

namespace Server\Manager;

use Server\Core\Init;
use Server\Core\Template;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Page
 */
class TemplateManager
{
    protected $view;
    protected $init;

    function __construct(Template $view, Init $init)
    {
        $this->view = $view;
        $this->init = $init;
    }

    /**
     * @param $name
     * @param $title
     * @param string $header
     * @param string $footer
     * @param bool $isScrollUp
     * @return $this
     */
    public function showPage($name, $title, $ajax = false, $header = 'header', $footer = 'footer', $isScrollUp = true) {
        global $settings;
        $this->setTitle($title);
        if (!$ajax)
            $this->showHeaderPage($header);

        if ($ajax)
            echo '<spa-title>' . $title . ' · ' . $settings->getTitle() . '</spa-title>';

        $this->showBlockPage($name);
        if (!$ajax)
            $this->showFooterPage($footer, $isScrollUp);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function showBlockPage($name) {
        echo $this->view->display($name . $this->init->config->Template);
        return $this;
    }

    /**
     * @param string $header
     * @return $this
     */
    public function showHeaderPage($header = 'header') {
        echo $this->view->display($header . $this->init->config->Template);
        return $this;
    }

    /**
     * @param string $footer
     * @param bool $isScrollUp
     * @return $this
     */
    public function showFooterPage($footer = 'footer', $isScrollUp = true) {
        $this->view->set('isScrollUp', $isScrollUp);
        echo $this->view->display($footer . $this->init->config->Template);
        return $this;
    }

    /**
     * @return $this
     */
    public function showError404Page() {
        header("HTTP/1.0 404 Not Found");
        $this->showPage('errors/404' ,'Error 404 ;c');
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title) {
        global $settings;
        $this->view->set('titleHtml', $title . ' · ' . $settings->getTitle());
        return $this;
    }
}