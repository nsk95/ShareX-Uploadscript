<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git github.com/nsk95
 */
require_once("./config.php");

/**
 * @description get user by secret-key
 * @param string $secret
 * @param array $a_allowedUser
 */
function getUser(string $secret, array $a_allowedUser) {
    foreach($a_allowedUser as $user) {
        if($user['key'] == $secret) {
            return $user;
        }
    }
    return false;
}

/**
 * @description function to generate random string with valuable length
 * @param integer $stringLen
 */
function randomString(int $stringLen) {
    $pool       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $genString  = '';

    for ($i = 0; $i < $stringLen; $i++) { 
        $genString .= substr($pool, random_int(0, strlen($pool) -1), 1);
    }
    return $genString;
}

/**
 * @description check if mime-type is valid/in array
 * @param $file
 * @param array $a_allowedMime
 */
function getValidMime($file, array $a_allowedMime) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $type = finfo_file($finfo, $file);
    finfo_close($finfo);

    return in_array($type, $a_allowedMime);
}

/**
 * @description return error 
 * @param integer $code
 */
function returnError(int $code) {
    http_response_code($code); 
    die();
}

/**
 * @description move file 
 * @param array $user
 * @param $file
 * @param boolean $randomName
 * @param integer $stringLen
 * @param string $baseUrl
 * @param string $uploadDir
 */
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

/**
 * @description scan all files in specified dir
 * @param string $uploadDir
 * @param string $baseUrl
 */
function scanAll(string $uploadDir, string $baseUrl) {
    $files = array();

    if($userJailed == true) {
        $scanDir = $uploadDir."/".$userDir;
    }
    else {
        $scanDir = $uploadDir;
    }

    foreach(scandir($scanDir) as $d) {
        if($d == false || substr($d, 0, 1) == ".") {
            continue;
        }
        if(is_dir($scanDir."/".$d)) {
            $files[] = array (
                "name"  => $d,
                "type"  => "folder",
                "path"  => $baseUrl.$scanDir."/".$d."/",
                "items" => scanAll($scanDir."/".$d, $baseUrl)
            );
        }
        else {
            $files[] = array(
                "name"  => $d,
                "type"  => "file",
                "path"  => $baseUrl.$scanDir."/".$d,
                "realpath" => realpath($scanDir."/".$d)
            );
        }
    }
    return $files;
}

/**
 * @description list all files from array
 * @param array $files
 * @param string $secret
 * @param string $baseUrl
 * @param string $uploadDir
 */
function listAll(array $files, $secret, $baseUrl, $uploadDir) {
    echo '<div class="row">';
    echo '<div class="col-sm-12">';
    echo '<ul class="list-group">';
    foreach($files as $f) {
        if($f['type'] == "folder") {
            echo '<li class="list-group-item list-group-item-primary"><p class="text-justify">';
            echo '<div class="row">';
            echo '<div class="col-sm-6">';
            echo $f['name'];
            echo '</div>';
            echo '<div class="col-sm-6">';
            echo ' <a class="btn btn-secondary" href="'.$f["path"].'">Link</a>';
            echo '</div>';
            echo '</div>';
            listAll($f['items'], $secret, $baseUrl, $uploadDir);
            echo '</p></li>';
        }
        else {
            echo '<li class="list-group-item list-group-item-action list-group-item-secondary"><p class="text-justify">';
            echo '<div class="row">';
            echo '<div class="col-sm-6">';
            echo $f['name'];
            echo '</div>';
            echo '<div class="col-sm-3">';
            echo '<a class="btn btn-secondary" href="'.$f["path"].'">Link</a>';
            echo '</div>';
            echo '<div class="col-sm-3">';
            echo '<a class="btn btn-danger" href="'.$baseUrl.'/delete.php?secret='.$secret.'&file='.$f['name'].'">Delete</a>';
            echo "</p></div></div></li>";
        }
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
}

/**
 * @description delete file
 * @param array $files
 * @param string $filename
 * @param integer $count
 */
function deleteFile(array $files, string $filename, $count = 0) {
    $count = 0;
    foreach($files as $f) {
        if($f['type'] == 'folder') {
            deleteFile($f['items'], $filename, $count);
        }
        else {
            if($f['name'] == $filename) {
                unlink($f['realpath']);
                $count++;
            }
        }
    }
    if($count > 0) {
        echo '<p>Erfolgreich '.$count.' Dateien gelöscht.</p>';
    }
}