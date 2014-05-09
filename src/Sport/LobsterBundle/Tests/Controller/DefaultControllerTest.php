<?php

namespace Sport\LobsterBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepageContainsGreetingAndLinkToFootballNews()
    {
        $crawler = $this->fetchUrl('/');
        $this->assertTrue($crawler->filter('html:contains("Hello!")')->count() > 0);
        $this->assertTrue($crawler->filter('a:contains("Football News")')->count() > 0);
    }

    public function testFootballNewsPageContainsCorrectElements()
    {
        $crawler = $this->fetchUrl('/football');
        $this->assertTrue($crawler->filter('h1:contains("Sky Sports | Football")')->count() > 0);
        $this->assertTrue($crawler->filter('img[src="http://www.skysports.com/Images/skysports/site/ss-logo-07.gif"]')->count() > 0);
        $this->assertEquals(19, $crawler->filter('article')->count());
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function fetchUrl($url)
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', $url);

        return $crawler;
    }
}
