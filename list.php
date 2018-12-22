<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git github.com/nsk95
 */

require_once("./functions.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Alle files</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script> -->
</head>
<body>
    
    <?php
        if(isset($_GET['secret'])) {
            $user = getUser($_GET['secret'], $a_allowedUser);
            if($user == false) {
                echo "<h1>wrong secret</h1>";
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

                $files      = scanAll($scanDir, $baseUrl);
                echo "<h1>List:</h1>";
                listAll($files, $_GET['secret'], $baseUrl, $uploadDir);
            }
        }
        else {
            echo '<h1>secret missing</h1>';
            returnError(403);
        }
       
        ?>
    </ul>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>

