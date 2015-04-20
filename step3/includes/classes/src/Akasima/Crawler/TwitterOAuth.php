<?php
namespace Akasima\Crawler;

use Abraham\TwitterOAuth\TwitterOAuth as Origin;

class TwitterOAuth extends Origin
{
    public function __construct($config)
    {
        parent::__construct(
            $config['twitter']['consumerKey'],
            $config['twitter']['consumerSecret'],
            $config['twitter']['accessToken'],
            $config['twitter']['accessTokenSecret']
        );
    }
}