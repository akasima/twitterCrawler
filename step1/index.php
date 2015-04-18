<?php

include_once('configs/app.php');
include_once('includes/func.php');

$dns = 'mysql:dbname='.$database['database'].';host='.$database['host'];
$pdo = new PDO($dns, $database['id'], $database['pw']);

$UTC = new DateTimeZone("UTC");
$newTZ = new DateTimeZone("Asia/Seoul");
date_default_timezone_set("Asia/Seoul");

// autoload가 없넨..
include_once('vendor/abraham/twitteroauth/src/Util/JsonDecoder.php');
include_once('vendor/abraham/twitteroauth/src/Config.php');
include_once('vendor/abraham/twitteroauth/src/Consumer.php');
// include 할 때 순서가 있음.. HmacSha1 보다 먼저 load 되야 함.
include_once('vendor/abraham/twitteroauth/src/SignatureMethod.php');
include_once('vendor/abraham/twitteroauth/src/HmacSha1.php');
include_once('vendor/abraham/twitteroauth/src/Request.php');
include_once('vendor/abraham/twitteroauth/src/Response.php');
include_once('vendor/abraham/twitteroauth/src/Token.php');
include_once('vendor/abraham/twitteroauth/src/TwitterOAuth.php');
include_once('vendor/abraham/twitteroauth/src/TwitterOAuthException.php');
include_once('vendor/abraham/twitteroauth/src/Util.php');


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
?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <style>
        /* Space out content a bit */
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        /* Everything but the jumbotron gets side spacing for mobile first views */
        .header,
        .marketing,
        .footer {
            padding-right: 15px;
            padding-left: 15px;
        }

        /* Custom page header */
        .header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e5e5;
        }
        /* Make the masthead heading the same height as the navigation */
        .header h3 {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 40px;
        }

        /* Custom page footer */
        .footer {
            padding-top: 19px;
            color: #777;
            border-top: 1px solid #e5e5e5;
        }

        /* Customize container */
        @media (min-width: 768px) {
            .container {
                max-width: 730px;
            }
        }
        .container-narrow > hr {
            margin: 30px 0;
        }

        /* Main marketing message and sign up button */
        .jumbotron {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
        }
        .jumbotron .btn {
            padding: 14px 24px;
            font-size: 21px;
        }

        /* Supporting marketing content */
        .marketing {
            margin: 40px 0;
        }
        .marketing p + h4 {
            margin-top: 28px;
        }

        /* Responsive: Portrait tablets and up */
        @media screen and (min-width: 768px) {
            /* Remove the padding we set earlier */
            .header,
            .marketing,
            .footer {
                padding-right: 0;
                padding-left: 0;
            }
            /* Space out the masthead */
            .header {
                margin-bottom: 30px;
            }
            /* Remove the bottom border on the jumbotron for visual effect */
            .jumbotron {
                border-bottom: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <li role="presentation" class="active"><a href="./">Home</a></li>
                <li role="presentation"><a href="./week.php">Week</a></li>
            </ul>
        </nav>
        <h3 class="text-muted">Twitter Crawler</h3>
    </div>
    <div class="jumbotron">
        <h1>Today Tweet</h1>
        <p class="lead"></p>
        <p><a class="btn btn-lg btn-success" href="#" role="button">Sign up!</a></p>
    </div>


    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Who</th>
                <th>Content</th>
                <th>Retweet Count</th>
                <th>Created at</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $index => $row) {
            ?>
            <tr>
                <td><?php echo $index + 1?></td>
                <td>
                    <a href="https://twitter.com/<?php echo $users[$row['user_id']]['screen_name']?>" target="_blank">
                        <img src="<?php echo $users[$row['user_id']]['profile_image_url']?>" />
                    </a>
                    <p><?php echo $users[$row['user_id']]['name']?></p>
                </td>
                <td>
                    <?php echo $row['content']?>
                    <p style="text-align:right;"><a href="https://twitter.com/<?php echo $users[$row['user_id']]['screen_name']?>/status/<?php echo $row['id']?>" target="_blank"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></p>
                </td>
                <td><?php echo $row['retweet_count']?></td>
                <td><?php echo shortTime($row['created_at'])?></td>
            </tr>
        <?php
        }   // EOF foreach ($rwos as $row) {
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

