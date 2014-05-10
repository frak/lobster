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
        $articleCount = 18;

        $crawler = $this->fetchUrl('/football');
        $this->assertTrue($crawler->filter('h1:contains("Sky Sports | Football")')->count() > 0);
        $this->assertTrue(
            $crawler->filter('img[src="http://www.skysports.com/Images/skysports/site/ss-logo-07.gif"]')->count() > 0
        );
        $this->assertEquals($articleCount, $crawler->filter('article')->count());
        $this->assertTrue($crawler->filter('article .title')->count() == $articleCount);
        $this->assertTrue($crawler->filter('article .description')->count() == $articleCount);
        $this->assertTrue($crawler->filter('article .pubDate')->count() == $articleCount);
        $this->assertTrue($crawler->filter('article .category')->count() == $articleCount);
    }

    public function testReportCategoryPageOnlyReturnsReportNewsItems()
    {
        $crawler    = $this->fetchUrl('/football/report');
        $categories = $crawler->filterXPath('//article/category');
        foreach ($categories as $cat) {
            if ($cat->nodeValue !== 'Report') {
                $this->fail('News that was not a report');
            }
        }
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
