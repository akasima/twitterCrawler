<?php

$crawler = new Crawler();

$con = $crawler->oauth($config);

// get home time line
$items = $crawler->homeTimeLine($con);
$crawler->storeTweets($items);

// get home time line
$items = $crawler->userTimeLine($con, [
    'user_id' => '152966047',   // 수지 트위터 회원 아이디.. 수지님에게.. 좀 미안..
    'count' => 200,
]);
$crawler->storeMyTweets($items);

$rows = $crawler->getRecently();

$users = $crawler->loadUsers();