<?php

$config = [];

$config['database'] = $database = [
    'host' => 'localhost',
    'database' => 'twitter_crawler',
    'id' => 'root',
    'pw' => 'root',
];


$config['twitter'] = $twitter = [
    'consumerKey' => 'jGH6FMMswRV0BNUct7kTTQ',
    'consumerSecret' => 'aWbYiIzndH9QO78lDmlx5RKjvfvIeXvEJ3pBfKIp8w',
    'accessToken' => '2275553054-1iXksTSdLhp514mhCZmP5QfdDbuowbIvYvDIVqa',
    'accessTokenSecret' => 'ljW34Lt2VDZKE5HB05YxqmMF4WZowqMt85i3hdhC9ljQz',
];

$dns = 'mysql:dbname='.$database['database'].';host='.$database['host'];
$config['pdo'] = $pdo = new PDO($dns, $database['id'], $database['pw']);

$config['utc'] = $UTC = new DateTimeZone("UTC");
$config['newTZ'] = $newTZ = new DateTimeZone("Asia/Seoul");
date_default_timezone_set("Asia/Seoul");


$curTime = time();
$oneDayTime = 60 * 60 * 24;
$oneHourTime = 60 * 60;


$paths = explode('/',
    str_replace(
        str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']),
        '',
        $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']
    )
);

$config['controllerName'] = $controllerName = $paths[0] ? $paths[0] : 'index';

return $config;