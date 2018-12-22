<?php
/**
 * @description simple share-x uplaod script
 * @url spyro.xyz
 * @git github.com/nsk95
 */

// config vars
$baseUrl    = "http://yourpage.com/";   // website-url
$uploadDir  = "uploads";           // upload-dir
$randomName = true;                // random filename or use real filename 
$stringLen  = 8;                    // if randomName is true define length of random string

$a_allowedUser = array(
    array(
        "username"  => 'admin',
        "key"       => '7qpraQu8sU3nw1QTTkEn',
        "allfiles"  => true,
        "dirname"   => 'admin',
        "jailuser"  => false
    ),
    array(
        "username"  => 'test',
        "key"       => 'BcrT3vfm2J56Wr7TjPTg',
        "allfiles"  => false,
        "dirname"   => '',
        "jailuser"  => true
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