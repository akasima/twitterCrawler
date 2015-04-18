<?php

include_once('configs/app.php');
include_once('includes/func.php');


$dns = 'mysql:dbname='.$database['database'].';host='.$database['host'];
$pdo = new PDO($dns, $database['id'], $database['pw']);

$UTC = new DateTimeZone("UTC");
$newTZ = new DateTimeZone("Asia/Seoul");
date_default_timezone_set("Asia/Seoul");

$curTime = time();
$oneDayTime = 60 * 60 * 24;
$oneHourTime = 60 * 60;

// autoload가 없넨..
include_once('vendor/autoload.php');


$con = new Abraham\TwitterOAuth\TwitterOAuth(
    $twitter['consumerKey'],
    $twitter['consumerSecret'],
    $twitter['accessToken'],
    $twitter['accessTokenSecret']
);
$con->setTimeouts(30, 30);

// get home time line
$items = $con->get('statuses/home_timeline', [
    'count' => 200,
]);

if (isset($items->errors)) {
    foreach( $items->errors as $error) {
        echo $error->message . "<br/>";
    }
    exit;
}

foreach ($items as $item) {

    $prepare = "SELECT * FROM user WHERE id=:id";
    $statement = $pdo->prepare($prepare);
    $statement->execute([
        ':id' => $item->user->id
    ]);

    if ($statement->rowCount() == 0) {
        $prepare = "INSERT INTO user (id, screen_name, name, profile_image_url) VALUES (:id, :screen_name, :name, :profile_image_url)";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':name' => $item->user->name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    } else {
        $prepare = "UPDATE user SET screen_name=:screen_name, name=:name, profile_image_url=:profile_image_url WHERE id=:id";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':name' => $item->user->name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    }


    $prepare = "SELECT * FROM tweet WHERE id=:id";
    $statement = $pdo->prepare($prepare);
    $statement->execute([
        ':id' => $item->user->id +123
    ]);

    if ($statement->rowCount() == 0) {
        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "INSERT INTO tweet (id, user_id, screen_name, content, retweet_count, created_at ) VALUES (:id, :user_id, :screen_name, :content, :retweet_count, :created_at) ";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->id,
            ':user_id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    } else {
        $prepare = "UPDATE tweet SET screen_name=:screen_name, content=:content, retweet_count=:retweet_count WHERE id=:id ";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }
}


// get user time line of 'missA_suzy'
$items = $con->get('statuses/user_timeline', [
    'user_id' => '152966047',
    'count' => 200,
]);

if (isset($items->errors)) {
    foreach( $items->errors as $error) {
        echo $error->message . "<br/>";
    }
    exit;
}

foreach ($items as $item) {

    $prepare = "SELECT * FROM user WHERE id=:id";
    $statement = $pdo->prepare($prepare);
    $statement->execute([
        ':id' => $item->user->id
    ]);

    if ($statement->rowCount() == 0) {
        $prepare = "INSERT INTO user (id, screen_name, profile_image_url) VALUES (:id, :screen_name, :profile_image_url)";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    } else {
        $prepare = "UPDATE user SET screen_name=:screen_name, profile_image_url=:profile_image_url WHERE id=:id";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    }


    $prepare = "SELECT * FROM tweet WHERE id=:id";
    $statement = $pdo->prepare($prepare);
    $statement->execute([
        ':id' => $item->user->id +123
    ]);

    if ($statement->rowCount() == 0) {
        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "INSERT INTO my_tweet (id, user_id, screen_name, content, retweet_count, created_at ) VALUES (:id, :user_id, :screen_name, :content, :retweet_count, :created_at) ";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->id,
            ':user_id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    } else {
        $prepare = "UPDATE my_tweet SET screen_name=:screen_name, content=:content, retweet_count=:retweet_count WHERE id=:id ";
        $statement = $pdo->prepare($prepare);
        $statement->execute([
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }
}

//$laterCreatedAt = date('Y-m-d H:i:s', strtotime("-1 week"));
$laterCreatedAt = date('Y-m-d H:i:s', strtotime("-1 day"));
$limit = 20;
$prepare = "SELECT * FROM tweet WHERE created_at > :later_created_at ORDER BY retweet_count DESC limit :limit";
//$prepare = "SELECT * FROM tweet WHERE created_at > :later_created_at ORDER BY id DESC limit :limit";
$statement = $pdo->prepare($prepare);
$statement->bindValue(':later_created_at', $laterCreatedAt, PDO::PARAM_STR);
$statement->bindValue(':limit', $limit, PDO::PARAM_INT);
$statement->execute();

$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
$userIds = [];
foreach ($rows as $index => $row) {
    $userId = (int)$row['user_id'];
    if (in_array($userId, $userIds) === false) {
        $parameterName = ':id' . $index;
        $userIds[$parameterName] = $userId;
    }
}

$ids = implode(',', array_keys($userIds));

$prepare = "SELECT * FROM user WHERE id in ($ids)";
$statement = $pdo->prepare($prepare);
foreach ($userIds as $parameterName => $userId) {
    $statement->bindValue($parameterName, $userId, PDO::PARAM_INT);
}

$statement->execute();
$users = [];
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $users[$row['id']] = $row;
}

include('./resources/views/index.php');
