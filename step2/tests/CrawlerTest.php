<?php

include_once('configs/app.php');
include_once('includes/helpers.php');

class CrawlerTest extends PHPUnit_Framework_TestCase
{

    protected $crawler;
    protected $con;

    public function setUp()
    {
        $this->getCrawler();
    }

    private function getCrawler()
    {
        global $config;

        $this->crawler = new Crawler();
        $this->con = $this->crawler->oauth($config);
    }

    /**
     * 글로벌을 사용할 수가 없네요..
     *
     * @backupGlobals disabled
     */
    public function testGetHomeTimeLine()
    {
        $items = $this->crawler->homeTimeLine($this->con);

        // 에러가 없으면 정상 처리
        $this->assertFalse(isset($items->errors));
    }

    /**
     * @backupGlobals disabled
     */
    public function testGetUserTimeLine()
    {
        $items = $this->crawler->userTimeLine($this->con, [
            'user_id' => '152966047',
            'count' => 200,
        ]);

        // 에러가 없으면 정상 처리
        $this->assertFalse(isset($items->errors));
    }

    /**
     * @backupGlobals disabled
     */
    public function testInsertHomeTimeLine()
    {
        $items = $this->crawler->homeTimeLine($this->con);

        $this->crawler->storeTweets($items);

        // 데이터를 넣는데.. 어떻게 테스트 할까??
        $this->assertFalse(isset($items->errors));
    }
}