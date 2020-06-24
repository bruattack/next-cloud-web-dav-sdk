#NextCloudWebDavSdk#
This is a simple wrapper around sabre/dav (https://sabre.io/) sepcifically for the interaction with the Nextcloud APIs (WebDAV and Share).

It is a fork of https://github.com/SatanaKonst/next-cloud-web-dav-sdk

$sdk = new \NextCloudWebDavSdk\NextCloudWebDavSdk(
    'http://192.168.0.1',
    'login',
    'pass'
);
$response = $sdk->webDav->getListingFolder();
$response = $sdk->share->createShare();

All class methods are listed below.

##WebDav##

Nextcloud WebDAV API docs:
https://docs.nextcloud.com/server/latest/developer_manual/client_apis/WebDAV/index.html

###Authorization###
$webDav = new \NextCloudWebDavSdk\WebDav\Server(
    'host',
    'login',
    'pass'
);

###List files in a folder###
$response = $webDav->getListingFolder(
    '/path/to/file'
);

###СFile download###
$response = $webDav->downloadFile(
    '/path/to/download/file',
    'path/to/save/file'
);


###File upload###
$response = $webDav->uploadFile(
    'path/to/upload/file/test_upload_pdf.pdf',
    '/path/for/save/file/'
);
    
    
    

###Create a folder###
$response = $webDav->createFolder(
    '/path/to/new/folder/',
);

###Delete files/folders###
$response = $webDav->removeFileOrDirectory(
    '/path/to/file/or/directory',
);

###Move files/folders###
$response = $webDav->moveFileOrDirectory(
    '/path/file/to/move',
    '/path/file/to/destination'
);

###Copy files/folders###
$response = $webDav->copyFileOrDirectory(
    '/path/file/to/copy',
    '/path/file/to/destination'
);

##Share##

Nextcloud Share/OCS API docs:
https://docs.nextcloud.com/server/latest/developer_manual/client_apis/OCS/ocs-share-api.html

###Authorization###
$share = new \NextCloudWebDavSdk\OCS\Share(
    'host',
    'login',
    'pass'
);

###Create a share###
$response = $share->createShare(
    'path/to/share/file'
);

###Remove a share###
$response = $share->removeShare(
    'shareID'
);


###List shares###
$response = $share->getShares(
     'path/to/share/file'
);

###Update a share###
$response = $share->updateShare(
    'shareID'
);

