<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git github.com/nsk95
 */
 require_once("./functions.php");

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