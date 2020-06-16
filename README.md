##WebDav\Server##

Документация по работе с WebDav
https://docs.nextcloud.com/server/latest/developer_manual/client_apis/WebDAV/index.html

###Авторизация###
$webDav = new \NextCloudWebDavSdk\WebDav\Server(
    'host',
    'login',
    'pass'
);

###Получить список файлов###
$response = $webDav->getListingFolder(
    '/path/to/file'
);

###Скачать файл###
$response = $webDav->downloadFile(
    '/path/to/download/file',
    'path/to/save/file'
);


###Загрузка файлов###
$response = $webDav->uploadFile(
    'path/to/upload/file/test_upload_pdf.pdf',
    '/path/for/save/file/'
);
    
    
###Создать папку###
$response = $webDav->createFolder(
    '/path/to/new/folder/',
);

###Удалить файл или директорию###
$response = $webDav->removeFileOrDirectory(
    '/path/to/file/or/directory',
);

###Переместить файл или дирректорию###
$response = $webDav->moveFileOrDirectory(
    '/path/file/to/move',
    '/path/file/to/destination'
);

###Копировать файл или директорию###
$response = $webDav->copyFileOrDirectory(
    '/path/file/to/copy',
    '/path/file/to/destination'
);


##OCS\Share##

Документация по шарингу 
https://docs.nextcloud.com/server/latest/developer_manual/client_apis/OCS/ocs-share-api.html

###Авторизация###
$share = new \NextCloudWebDavSdk\OCS\Share(
    'host',
    'login',
    'pass'
);

###Расшарить файл###
$response = $share->createShare(
    'path/to/share/file'
);

###Удалить шару###
$response = $share->removeShare(
    'shareID'
);


###Получить шару###
$response = $share->getShares(
     'path/to/share/file'
);

###Обновить шару###
$response = $share->updateShare(
    'shareID'
);

