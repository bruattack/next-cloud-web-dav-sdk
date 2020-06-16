<?php

namespace NextCloudWebDavSdk;

use NextCloudWebDavSdk\OCS\Share;
use NextCloudWebDavSdk\WebDav\WebDav;

class NextCloudWebDavSdk
{
    private $baseUrl;
    private $login;
    private $pass;

    public $server;
    public $share;

    /**
     * NextCloudWebDavSdk constructor.
     * @param $baseUrl
     * @param $login
     * @param $pass
     */
    public function __construct($baseUrl,$login,$pass)
    {
        $this->baseUrl = $baseUrl;
        $this->login = $login;
        $this->pass = $pass;

//        $this->server = new Server($baseUrl,$login,$pass);
//        $this->share = new Share($login,$pass);
    }



}