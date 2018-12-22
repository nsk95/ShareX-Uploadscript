<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git github.com/nsk95
 */
require_once("./functions.php");

if(isset($_GET['secret']) && isset($_GET['file'])) {
    $user = getUser($_GET['secret'], $a_allowedUser);
    if($user == false) {
        returnError(403);
    }
    else {
        $files = array();

        if($user['dirname'] != '') {
            $userDir = $user['dirname'];
        }
        else {
            $userDir = $user['username'];
        }
        
        $userJailed = $user['jailuser'];
        if($userJailed == true) {
            $scanDir = $uploadDir."/".$userDir;
        }
        else {
            $scanDir = $uploadDir;
        }
        $files = scanAll($scanDir, $baseUrl);
        
        deleteFile($files, $_GET['file']);
    }
}
else {
    returnError(403);
}

