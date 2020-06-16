<?php

namespace NextCloudWebDavSdk;


class NextCloudWebDavSdk
{
    public $webDav;
    public $share;

    /**
     * NextCloudWebDavSdk constructor.
     * @param $baseUrl
     * @param $login
     * @param $pass
     */
    public function __construct($host,$login,$pass)
    {
        $this->baseUrl = $host;
        $this->login = $login;
        $this->pass = $pass;

        $this->webDav = new WebDav($host,$login,$pass);
        $this->share = new Share($host,$login,$pass);
    }

}