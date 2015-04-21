<?php

class CrawlerRepository
{

    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function countUser($item)
    {
        $prepare = "SELECT * FROM user WHERE id=:id";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id
        ]);

        return $statement->rowCount();
    }

    public function insertUser($item)
    {
        $prepare = "INSERT INTO user (id, screen_name, name, profile_image_url) VALUES (:id, :screen_name, :name, :profile_image_url)";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':name' => $item->user->name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    }

    public function updateUser($item)
    {
        $prepare = "UPDATE user SET screen_name=:screen_name, name=:name, profile_image_url=:profile_image_url WHERE id=:id";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':name' => $item->user->name,
            ':profile_image_url' => $item->user->profile_image_url,
        ]);
    }

    public function countTweet($item)
    {
        $prepare = "SELECT * FROM tweet WHERE id=:id";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id
        ]);

        return $statement->rowCount();
    }

    public function insertTweet($item)
    {
        global $newTZ, $UTC;

        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "INSERT INTO tweet (id, user_id, screen_name, content, retweet_count, created_at ) VALUES (:id, :user_id, :screen_name, :content, :retweet_count, :created_at) ";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->id,
            ':user_id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }

    public function updateTweet($item)
    {
        global $newTZ, $UTC;

        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "UPDATE tweet SET screen_name=:screen_name, content=:content, retweet_count=:retweet_count WHERE id=:id ";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }

    public function getTweeets($params)
    {
        if (isset($params['later_created_at']) !== true) {
            throw new \Exception('Require later_created_at value');
        }

        $prepare = "SELECT * FROM tweet WHERE created_at > :later_created_at ORDER BY created_at DESC limit :limit";
        $statement = $this->pdo->prepare($prepare);


        $statement->bindValue(':later_created_at', $params['later_created_at'], PDO::PARAM_STR);
        if (isset($params['limit']) !== true) {
            $params['limit'] = 20;
        }
        $statement->bindValue(':limit', $params['limit'], PDO::PARAM_INT);

        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function countMyTweet($item)
    {
        $prepare = "SELECT * FROM my_tweet WHERE id=:id";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->user->id
        ]);

        return $statement->rowCount();
    }

    public function insertMyTweet($item)
    {
        global $newTZ, $UTC;

        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "INSERT INTO my_tweet (id, user_id, screen_name, content, retweet_count, created_at ) VALUES (:id, :user_id, :screen_name, :content, :retweet_count, :created_at) ";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':id' => $item->id,
            ':user_id' => $item->user->id,
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }

    public function updateMyTweet($item)
    {
        global $newTZ, $UTC;

        $date = new DateTime( $item->created_at, $UTC );
        $date->setTimezone( $newTZ );
        $created_at = $date->format('Y-m-d H:i:s');

        $prepare = "UPDATE my_tweet SET screen_name=:screen_name, content=:content, retweet_count=:retweet_count WHERE id=:id ";
        $statement = $this->pdo->prepare($prepare);
        $statement->execute([
            ':screen_name' => $item->user->screen_name,
            ':content' => $item->text,
            ':retweet_count' => $item->retweet_count,
            ':created_at' => $created_at
        ]);
    }

    public function getUsersByIds($userIds)
    {
        $ids = implode(',', array_keys($userIds));

        $prepare = "SELECT * FROM user WHERE id in ($ids)";
        $statement = $this->pdo->prepare($prepare);
        foreach ($userIds as $parameterName => $userId) {
            $statement->bindValue($parameterName, $userId, PDO::PARAM_INT);
        }
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}