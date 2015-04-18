<?php

include_once('configs/app.php');
include_once('includes/func.php');

$dns = 'mysql:dbname='.$database['database'].';host='.$database['host'];
$pdo = new PDO($dns, $database['id'], $database['pw']);

$UTC = new DateTimeZone("UTC");
$newTZ = new DateTimeZone("Asia/Seoul");
date_default_timezone_set("Asia/Seoul");

$laterCreatedAt = date('Y-m-d H:i:s', strtotime("-1 week"));
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
                <li role="presentation"><a href="./">Home</a></li>
                <li role="presentation" class="active"><a href="./week.php">Week</a></li>
            </ul>
        </nav>
        <h3 class="text-muted">Twitter Crawler</h3>
    </div>
    <div class="jumbotron">
        <h1>Weekly Hot</h1>
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

