<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git https://github.com/nsk95/ShareX-Uploadscript
 */

 // config vars
$baseUrl    = "yourpage.com/";   // website-url
$uploadDir  = "uploads";         // upload-dir
$randomName = true;              // random filename or use real filename 
$stringLen  = 8;                 // if randomName is true define length of random string

$a_allowedUser = array(
    array(
        "username"  => 'admin',
        "key"       => 'CGhzvQKrEVDE5tpUK6uN',
        "allfiles"  => true,
        "dirname"   => 'admin'
    ),
    array(
        "username"  => 'test',
        "key"       => 'BcrT3vfm2J56Wr7TjPTg',
        "allfiles"  => false,
        "dirname"   => ''
    )
);

$a_allowedMime = array(
	'image/gif',
	'image/jpeg',
	'image/pjpeg',
	'image/png',
	'image/tiff',
	'image/x-tiff',
	'image/tiff',
	'image/x-tiff',
    	'image/vnd.wap.wbmp',
    	'image/bmp',
    	'image/x-windows-bmp',
    	'video/mpeg',
    	'video/mp4',
    	'video/quicktime',
    	'video/webm',
    	'video/x-msvideo'
);

function getUser(string $secret, array $a_allowedUser) {
    foreach($a_allowedUser as $user) {
        if($user['key'] == $secret) {
            return $user;
        }
    }
    return false;
}

function randomString(int $stringLen) {
    $pool       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $genString  = '';

    for ($i = 0; $i < $stringLen; $i++) { 
        $genString .= substr($pool, random_int(0, strlen($pool) -1), 1);
    }
    return $genString;
}

function getValidMime($file, $a_allowedMime) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $type = finfo_file($finfo, $file);
    finfo_close($finfo);

    return in_array($type, $a_allowedMime);
}

function returnError(int $code) {
    http_response_code($code); 
    die();
}

function moveFile($user, $file, bool $randomName, int $stringLen, string $baseUrl, string $uploadDir) {
    $fileParts = pathinfo($file['name']);

    if($randomName == true) {
        $filename = randomString($stringLen);
    }
    else {
        $filename = random_int(100, 999).$fileParts['filename'];
    }

    if($user['dirname'] != '') {
        $userDir = $user['dirname'];
    }
    else {
        $userDir = $user['username'];
    }

    $path = __DIR__."/".$uploadDir."/";
    if(file_exists($path) == false || is_dir($path) == $path) {
        mkdir(__DIR__."/".$uploadDir, 0775);
    }
    $path .= $userDir."/";
    if(file_exists($path) == false || is_dir($path) == $path) {
        mkdir(__DIR__."/".$uploadDir."/".$userDir, 0775);
    }

    $moveSuccess = move_uploaded_file($file['tmp_name'], $path.$filename.'.'.$fileParts['extension']);

    if($moveSuccess == true) {
        echo $baseUrl.$uploadDir."/".$userDir."/".$filename.".".$fileParts['extension'];
        die();
    }
    else {
        returnError(500);
    }
}

if(isset($_POST['secret']) && isset($_FILES) && $_FILES['sharex']['error'] == 0) { 
    $user = getUser($_POST['secret'], $a_allowedUser);

    if($user != false) {
        $file = $_FILES['sharex'];

        if($user['allfiles'] == true) {

            if(isset($_POST['randomname'])) {
                $randomName = $_POST['randomname'];
            }
            if(isset($_POST['stringlen'])) {
                $stringLen = $_POST['stringlen'];
            }
            if(is_numeric($stringLen) == false) {
                returnError(400);
            }
            moveFile($user, $file, $randomName, $stringLen, $baseUrl, $uploadDir);
        }
        else {
            $valid = getValidMime($file['tmp_name'], $a_allowedMime);
            
            if($valid == false) {
                returnError(403);
            }
            else {
                if(isset($_POST['randomname'])) {
                    $randomName = $_POST['randomname'];
                }
                if(isset($_POST['stringlen'])) {
                    $stringLen = $_POST['stringlen'];
                }
                if(is_numeric($stringLen) == false) {
                    returnError(400);
                }
                moveFile($user, $file, $randomName, $stringLen, $baseUrl, $uploadDir);
            }
        }
    }
    else {
        returnError(403);
    }
}
else {
    returnError(400);
}
