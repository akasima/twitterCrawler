<?php

$crawler = new Akasima\Crawler\Crawler(
    new Akasima\Crawler\CrawlerRepository($pdo)
);

$rows = $crawler->getWeekly();

$users = $crawler->loadUsers();