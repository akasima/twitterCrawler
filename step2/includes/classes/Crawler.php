<?php

class Crawler
{

    protected $repo;

    protected $users = [];
    protected $userLoaded = false;

    public function __construct()
    {
        $this->repo = new CrawlerRepository();
    }

    public function oauth($config)
    {
        $con = new Abraham\TwitterOAuth\TwitterOAuth(
            $config['twitter']['consumerKey'],
            $config['twitter']['consumerSecret'],
            $config['twitter']['accessToken'],
            $config['twitter']['accessTokenSecret']
        );
        $con->setTimeouts(30, 30);

        return $con;
    }

    public function homeTimeLine($con, $options = ['count' => 200])
    {
        // get home time line
        $items = $con->get('statuses/home_timeline', $options);

        if (isset($items->errors)) {

            foreach( $items->errors as $error) {
                throw new \Exception($error->message);
            }
        }

        return $items;
    }

    public function userTimeLine($con, $options = ['count' => 200])
    {
        if (isset($options['user_id']) !== true) {
            throw new \Exception('User emtpy');
        }

        // get home time line
        $items = $con->get('statuses/user_timeline', $options);

        if (isset($items->errors)) {

            foreach( $items->errors as $error) {
                throw new \Exception($error->message);
            }
        }

        return $items;
    }

    public function storeTweets($items)
    {
        foreach ($items as $item) {
            $this->storeTweet($item);
        }
    }

    public function storeUser($item)
    {
        if ($this->repo->countUser($item) == 0) {
            $this->repo->insertUser($item);
        } else {
            $this->repo->updateUser($item);
        }
    }

    public function storeTweet($item)
    {
        $this->storeUser($item);

        if ($this->repo->countTweet($item) == 0) {
            $this->repo->insertTweet($item);
        } else {
            $this->repo->updateTweet($item);
        }
    }

    public function storeMyTweets($items)
    {
        foreach ($items as $item) {
            $this->storeMyTweet($item);
        }
    }

    public function storeMyTweet($item)
    {
        $this->storeUser($item);

        if ($this->repo->countMyTweet($item) == 0) {
            $this->repo->insertMyTweet($item);
        } else {
            $this->repo->updateMyTweet($item);
        }
    }

    public function getRecently($limit = 20)
    {
        $laterCreatedAt = date('Y-m-d H:i:s', strtotime("-1 day"));
        $rows = $this->repo->getTweeets([
            'later_created_at' => $laterCreatedAt,
            'limit' => $limit,
        ]);

        foreach ($rows as $row) {
            $this->addUsers($row['user_id']);
        }

        return $rows;
    }

    public function getWeekly($limit = 20)
    {
        $laterCreatedAt = date('Y-m-d H:i:s', strtotime("-1 week"));
        $rows = $this->repo->getTweeets([
            'later_created_at' => $laterCreatedAt,
            'limit' => $limit,
        ]);

        foreach ($rows as $row) {
            $this->addUsers($row['user_id']);
        }

        return $rows;
    }

    private function addUsers($userId)
    {
        if (isset($this->users[$userId]) === false) {
            $this->users[$userId] = null;
            $this->userLoaded = false;
        }
    }

    public function getUser($userId)
    {
        if ($this->userLoaded === false) {
            $this->loadUsers();
        }

        return $this->users[$userId];
    }

    public function loadUsers()
    {
        $this->userLoaded = true;

        $userIds = [];
        $index = 0;
        foreach ($this->users as $userId => $epmty) {
            $userId = (int)$userId;
            if (in_array($userId, $userIds) === false) {
                ++$index;
                $parameterName = ':id' . $index;
                $userIds[$parameterName] = $userId;
            }
        }
        $rows = $this->repo->getUsersByIds($userIds);

        foreach ($rows as $row) {
            $this->users[$row['id']] = $row;
        }

        return $this->users;
    }
}
