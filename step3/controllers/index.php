<?php

$crawler = new Akasima\Crawler\Crawler(
    new Akasima\Crawler\CrawlerRepository($pdo)
);

$crawler->oauth(new Abraham\TwitterOAuth\TwitterOAuth(
    $config['twitter']['consumerKey'],
    $config['twitter']['consumerSecret'],
    $config['twitter']['accessToken'],
    $config['twitter']['accessTokenSecret']
));

// get home time line
$items = $crawler->homeTimeLine();
$crawler->storeTweets($items);

// get home time line
$items = $crawler->userTimeLine([
    'user_id' => '152966047',   // 수지 트위터 회원 아이디.. 수지님에게.. 좀 미안..
    'count' => 200,
]);
$crawler->storeTweets($items);

$rows = $crawler->getRecently();

$users = $crawler->loadUsers();