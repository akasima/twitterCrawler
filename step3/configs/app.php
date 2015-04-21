<?php

include_once('../config.php');

$config = [];

$config['database'] = $database;

$config['twitter'] = $twitter;

$dns = 'mysql:dbname='.$database['database'].';host='.$database['host'];
$config['pdo'] = $pdo = new PDO($dns, $database['id'], $database['pw']);

$config['utc'] = $UTC = new DateTimeZone("UTC");
$config['newTZ'] = $newTZ = new DateTimeZone("Asia/Seoul");
date_default_timezone_set("Asia/Seoul");


$curTime = time();
$oneDayTime = 60 * 60 * 24;
$oneHourTime = 60 * 60;

// front control
$paths = explode('/',
    str_replace(
        str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']),
        '',
        $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']
    )
);

$config['controllerName'] = $controllerName = $paths[0] ? $paths[0] : 'index';

return $config;