<?php

use Mockery as m;

class CrawlerTest extends PHPUnit_Framework_TestCase
{

    protected $crawler;
    protected $con;
    protected $repo;

    public function setUp()
    {
        $this->getCrawler();
    }

    private function getCrawlerRepository()
    {
        $this->repo = m::mock('Akasima\Crawler\CrawlerRepository');
        $this->repo->shouldReceive('countUser')->andReturn();
        return $this->repo;
    }

    private function getOAuth()
    {
        $this->oauth = m::mock('Abraham\TwitterOAuth\TwitterOAuth');
        $this->oauth->shouldReceive('setTimeouts')->andReturn();

        $this->oauth->shouldReceive('get')->with('statuses/home_timeline', ['count' => 200])->andReturn('home_timeline');
        $this->oauth->shouldReceive('get')->withArgs(['statuses/user_timeline', ['user_id' => '1', 'count' => 200,]])->andReturn('user_timeline');

        return $this->oauth;
    }

    private function getCrawler()
    {
        $this->crawler = new Akasima\Crawler\Crawler(
            $this->getCrawlerRepository()
        );

        $this->crawler->oauth(
            $this->getOAuth()
        );

    }

    /**
     * 이 테스트에서 auth 가 정상 동작 하는지는 중요하지 않음.
     * 개발자가 예상하는대로 움직이는것이 중요함
     */
    public function testGetHomeTimeLine()
    {
        $items = $this->crawler->homeTimeLine();

        $this->assertEquals('home_timeline', $items);
    }

    public function testGetUserTimeLine()
    {
        $items = $this->crawler->userTimeLine([
            'user_id' => '1',
            'count' => 200,
        ]);

        $this->assertEquals('user_timeline', $items);
    }

    /**
     * user_id 를 지정하지 않아 에러 발생
     * @expectedException \Akasima\Crawler\Exceptions\UserIdRequiredException
     */
    public function testGetUserTimeLineException()
    {
        $this->crawler->userTimeLine([
            'count' => 200,
        ]);
    }
}