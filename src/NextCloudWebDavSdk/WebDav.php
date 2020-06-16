<?php


namespace NextCloudWebDavSdk;

use Sabre\DAV\Client;
use Sabre\DAV\Xml\Property\ResourceType;

use function Sabre\HTTP\decodePath;

class WebDav
{
    public $client;
    protected $baseUrl;

    public function __construct($host, $login, $pass)
    {
        $this->baseUrl = "$host/remote.php/dav/files/$login/";
        $this->client = new Client(array(
            'baseUri' => $this->baseUrl,
            'userName' => $login,
            'password' => $pass,
        ));
    }

    /**
     * @param string $folder
     * @return array
     * @throws \Sabre\HTTP\ClientHttpException
     */
    public function getListingFolder($folder = '')
    {
        $structures = $this->client->propFind(
            $folder,
            array(
                '{DAV:}getlastmodified',
                '{DAV:}getetag',
                '{DAV:}getcontenttype',
                '{DAV:}resourcetype',
                '{DAV:}getcontentlength',
                '{http://owncloud.org/ns}id',
                '{http://owncloud.org/ns}fileid',
                '{http://owncloud.org/ns}favorite',
                '{http://owncloud.org/ns}comments-href',
                '{http://owncloud.org/ns}comments-count',
                '{http://owncloud.org/ns}comments-unread',
                '{http://owncloud.org/ns}owner-id',
                '{http://owncloud.org/ns}owner-display-name',
                '{http://owncloud.org/ns}share-types',
                '{http://owncloud.org/ns}checksums',
                '{http://owncloud.org/ns}has-preview',
                '{http://owncloud.org/ns}size',
            ),
            1
        );

        $result = array();
        if (!empty($structures)) {
            foreach ($structures as $key => $structure) {

                $key = decodePath($key);
                $parsepath = pathinfo($key);


                $shareTypes = array();
                if (is_array($structure['{http://owncloud.org/ns}share-types'])) {
                    $shareTypes = array_column($structure['{http://owncloud.org/ns}share-types'], 'value');
                }
                if (is_object($structure['{DAV:}resourcetype'])) {
                    $resourcetype = $structure['{DAV:}resourcetype']->getValue();
                } else {
                    $resourcetype = array();
                }

                $result[] = array(
                    'path' => $key,
                    'element-name' => $parsepath['filename'],
                    'getlastmodified' => $structure['{DAV:}getlastmodified'],
                    'getetag' => $structure['{DAV:}getetag'],
                    'getcontenttype' => $structure['{DAV:}getcontenttype'],
                    'resourcetype' => $resourcetype,
                    'getcontentlength' => $structure['{DAV:}getcontentlength'],
                    'id' => $structure['{http://owncloud.org/ns}id'],
                    'fileid' => $structure['{http://owncloud.org/ns}fileid'],
                    'favorite' => $structure['{http://owncloud.org/ns}favorite'],
                    'comments-href' => $structure['{http://owncloud.org/ns}comments-href'],
                    'comments-count' => $structure['{http://owncloud.org/ns}comments-count'],
                    'comments-unread' => $structure['{http://owncloud.org/ns}comments-unread'],
                    'owner-id' => $structure['{http://owncloud.org/ns}owner-id'],
                    'owner-display-name' => $structure['{http://owncloud.org/ns}owner-display-name'],
                    'share-types' => $shareTypes,
                    'checksums' => $structure['{http://owncloud.org/ns}checksums'],
                    'has-preview' => $structure['{http://owncloud.org/ns}has-preview'],
                    'size' => $structure['{http://owncloud.org/ns}size'],
                );

            }
        }
        return $result;
    }

    /**
     * @param $path
     * @param $savePath
     * @return string
     * @throws \Exception
     */
    public function downloadFile($path, $savePath)
    {
        if (empty($path)) {
            throw new \Exception('Empty download file');
        }
        if (empty($savePath)) {
            throw new \Exception('Empty Save path');
        }
        $parsePath = pathinfo($path);

        $url = $this->baseUrl . $path;
        $response = $this->client->request('GET', $url);
        if ($response['statusCode'] != 200) {
            throw new \Exception('Error download file.' . $response['body']);
        }

        file_put_contents($savePath . '/' . $parsePath['basename'], $response['body']);

        return $savePath . '/' . $parsePath['basename'];
    }

    /**
     * @param $uploadFile
     * @param string $uploadPath
     * @return bool
     * @throws \Exception
     */
    public function uploadFile($uploadFile, $uploadPath = '')
    {
        if (empty($uploadFile)) {
            throw new \Exception('Empty upload file');
        }

        $parseUploadFile = pathinfo($uploadFile);

        $url = $this->baseUrl . $uploadPath . $parseUploadFile['basename'];

        $uploadFile = file_get_contents($uploadFile);

        $response = $this->client->request('PUT', $url, $uploadFile);

        if ($response['statusCode'] != 201) {
            throw new \Exception('Error upload file.' . $response['body']);
        }
        return true;
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function createFolder($path)
    {
        if (empty($path)) {
            throw new \Exception('Empty create folder path');
        }

        $url = $this->baseUrl . $path;
        $response = $this->client->request('MKCOL', $url);
        if ($response['statusCode'] != 201) {
            throw new \Exception('Error create folder.' . $response['body']);
        }
        return true;
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function removeFileOrDirectory($path)
    {
        if (empty($path)) {
            throw new \Exception('Empty path remove file or directory');
        }
        $url = $this->baseUrl . $path;
        $response = $this->client->request('DELETE', $url);
        if ($response['statusCode'] != 204) {
            throw new \Exception('Error remove file or directory.' . $response['body']);
        }

        return true;
    }

    /**
     * @param $move
     * @param $destination
     * @return bool
     * @throws \Exception
     */
    public function moveFileOrDirectory($move, $destination)
    {
        if (empty($move)) {
            throw new \Exception('Empty move path');
        }
        if (empty($destination)) {
            throw new \Exception('Empty destination path');
        }

        $movePath = $this->baseUrl . $move;
        $destinationPath = $this->baseUrl . $destination;

        $response = $this->client->request(
            'MOVE',
            $movePath,
            null,
            array(
                'Destination' => $destinationPath
            ));

        if ($response['statusCode'] != 201) {
            throw new \Exception('Error move file or directory.' . $response['body']);
        }

        return true;
    }

    /**
     * @param $copy
     * @param $destination
     * @return bool
     * @throws \Exception
     */
    public function copyFileOrDirectory($copy,$destination){
        if (empty($copy)) {
            throw new \Exception('Empty move path');
        }
        if (empty($destination)) {
            throw new \Exception('Empty destination path');
        }
        $copyPath = $this->baseUrl . $copy;
        $destinationPath = $this->baseUrl . $destination;

        $response = $this->client->request(
            'COPY',
            $copyPath,
            null,
            array(
                'Destination' => $destinationPath
            ));

        if ($response['statusCode'] != 201) {
            throw new \Exception('Error move file or directory.' . $response['body']);
        }

        return true;
    }

}