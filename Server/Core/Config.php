<?php
namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Config
 */
class Config
{
    protected $result;

    /**
     * @param $file
     * @return $this
     */
    public function getConfig($file) {
        $this->result = file_get_contents('config/' . $file . '.json');
        return $this;
    }

    /**
     * @return $this
     */
    public function getAppiAllConfig() {
        $this->result = file_get_contents('config/appi.json');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArrayResult() {
        return json_decode($this->result, true);
    }

    /**
     * @return mixed
     */
    public function getObjectResult() {
        return json_decode($this->result);
    }

    /**
     * @return mixed
     */
    public function getJsonResult() {
        return $this->result;
    }
}