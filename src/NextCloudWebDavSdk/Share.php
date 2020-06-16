<?php

namespace NextCloudWebDavSdk;

use Sabre\DAV\Client;

class Share
{
    protected $client;
    protected $baseUrl;

    public static $SHARRE_USER = 0;
    public static $SHARRE_GROUP = 1;
    public static $SHARRE_PUBLIC_LINK = 3;
    public static $SHARRE_EMAIL = 4;
    public static $SHARRE_FEDERATED_CLOUD = 6;
    public static $SHARRE_CIRCLE = 7;
    public static $SHARRE_TAKL_CONVERSATION = 10;


    public function __construct($host, $login, $pass)
    {
        $this->baseUrl = $host;
        $this->login = $login;
        $this->pass = $pass;

        $this->client = new Client(array(
            'baseUri' => $this->baseUrl,
            'userName' => $login,
            'password' => $pass,
        ));
    }

    /**
     * @param array $share
     * @return mixed
     * @throws \Exception
     * shareType - (int) 0 = user; 1 = group; 3 = public link; 4 = email; 6 = federated cloud share; 7 = circle; 10 = Talk conversation
     */
    public function createShare(
        string $path,
        int $shareType,
        string $shareWith = '',
        bool $publicUpload = true,
        string $password = '',
        int $permissions = 31,
        string $expireDate = ''
    )
    {
        $url = $this->baseUrl . '/ocs/v2.php/apps/files_sharing/api/v1/shares';
        if (empty($path)) {
            throw new \Exception('Empty share path');
        }
        if (empty($shareType)) {
            throw new \Exception('Empty share type');
        }


        if (!empty($expireDate)) {
            $expireDate = new \DateTime($expireDate);
            $expireDate = $expireDate->format('Y-m-d');
        }

        $response = $this->sendQuery('POST', $url, array(
            'path' => $path,
            'shareType' => $shareType,
            'shareWith' => $shareWith,
            'publicUpload' => $publicUpload,
            'password' => $password,
            'permissions' => $permissions,
            'expireDate' => $expireDate
        ));
        $response = $this->parseOcs($response['body']);

        if ($response->meta->statuscode != 200) {
            throw new \Exception('Error share file. Code:' . $response->meta->statuscode . ' Message:' . $response->meta->message);
        }

        return $response;
    }

    /**
     * @param $shareId
     * @return bool
     * @throws \Exception
     */
    public function removeShare($shareId)
    {
        $url = $this->baseUrl . '/ocs/v2.php/apps/files_sharing/api/v1/shares/' . $shareId;

        if (empty($shareId)) {
            throw new \Exception('Empty shareId');
        }

        $response = $this->sendQuery(
            'DELETE',
            $url,
            array(
                'share_id' => $shareId
            )
        );
        $response = $this->parseOcs($response['body']);
        if ($response->meta->statuscode != 200) {
            throw new \Exception('Error remove share link. Code:' . $response->meta->statuscode . ' Message:' . $response->meta->message);
        }

        return true;
    }

    /**
     * @param string $path
     * @param bool $reshares
     * @param bool $subfiles
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function getShares($path = '', $reshares = false, $subfiles = false)
    {
        $url = $this->baseUrl . '/ocs/v2.php/apps/files_sharing/api/v1/shares';
        $response = $this->sendQuery(
            'GET',
            $url,
            array(
                'path' => $path,
                'reshares' => $reshares,
                'subfiles' => $subfiles
            )
        );
        $response = $this->parseOcs($response['body']);
        if ($response->meta->statuscode != 200) {
            throw new \Exception('Error get shares. Code:' . $response->meta->statuscode . ' Message:' . $response->meta->message);
        }

        return $response;
    }

    /**
     * @param $shareId
     * @param int $permissions
     * @param string $password
     * @param bool $publicUpload
     * @param string $expireDate
     * @param string $note
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function updateShare(
        $shareId,
        int $permissions = 31,
        string $password = '',
        bool $publicUpload = true,
        string $expireDate = '',
        string $note = ''
    )
    {
        $url = $this->baseUrl . '/ocs/v2.php/apps/files_sharing/api/v1/shares/' . $shareId;
        if (empty($shareId)) {
            throw new \Exception('Empty shareId');
        }

        if (!empty($expireDate)) {
            $expireDate = new \DateTime($expireDate);
            $expireDate = $expireDate->format('Y-m-d');
        }

        $response = $this->sendQuery(
            'PUT',
            $url,
            array(
                'share_id' => $shareId,
                'permissions' => $permissions,
                'password' => $password,
                'publicUpload' => $publicUpload,
                'expireDate' => $expireDate,
                'note' => $note
            )
        );
        $response = $this->parseOcs($response['body']);
        if ($response->meta->statuscode != 200) {
            throw new \Exception('Error remove share link. Code:' . $response->meta->statuscode . ' Message:' . $response->meta->message);
        }

        return $response;
    }

    /**
     * @param $data
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    protected function parseOcs($data)
    {
        if (empty($data)) {
            throw new \Exception('Empty parse data');
        }
        return simplexml_load_string($data);
    }


    /**
     * @param string $method
     * @param string $url
     * @param array $body
     * @return array
     */
    protected function sendQuery(string $method, string $url, array $body)
    {

        $share = $this->client->request(
            $method,
            $url,
            http_build_query(
                $body
            ),
            array(
                'Authorization' => 'Basic ' . base64_encode("$this->login:$this->pass"),
                'OCS-APIRequest' => 'true',
            )
        );

        return $share;

    }
}