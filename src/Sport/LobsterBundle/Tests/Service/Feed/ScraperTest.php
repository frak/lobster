<?php
/**
 * ScraperTest.php
 *
 * User: mikey
 * Date: 08/05/2014
 * Time: 19:28
 */

namespace Sport\LobsterBundle\Tests\Service\Feed;

use Sport\LobsterBundle\Service\Feed\Scraper;
use Sport\LobsterBundle\Traits\TearDownTrait;
use \Mockery as m;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ScraperTest
 *
 * @package Sport\LobsterBundle\Tests\Service\Feed
 */
class ScraperTest extends \PHPUnit_Framework_TestCase
{
    use TearDownTrait;

    private $emptyChannel = <<<XML
<?xml version="1.0" encoding="iso-8859-1" ?>
<rss version="2.0">
    <channel>
        <title>Sky Sports | Football </title>
        <link>http://www.skysports.com</link>
        <description>Europa League News</description>
        <language>en-gb</language>
        <lastBuildDate>Sat, 26 Apr 2014 15:20:18 GMT</lastBuildDate>
        <copyright>Copyright 2014, BSKYB. All Rights Reserved.</copyright>
        <category>Football</category>
        <image>
            <title>Sky Sports</title>
            <url>http://www.skysports.com/Images/skysports/site/ss-logo-07.gif</url>
            <link>http://www.skysports.com</link>
        </image>
        <ttl>120</ttl>
    </channel>
</rss>
XML;

    private $realChannel = <<<XML
<?xml version="1.0" encoding="iso-8859-1" ?>
<rss version="2.0">
    <channel>
        <title>Sky Sports | Football </title>
        <link>http://www.skysports.com</link>
        <description>Europa League News</description>
        <language>en-gb</language>
        <lastBuildDate>Sat, 26 Apr 2014 15:20:18 GMT</lastBuildDate>
        <copyright>Copyright 2014, BSKYB. All Rights Reserved.</copyright>
        <category>Football</category>
        <image>
            <title>Sky Sports</title>
            <url>http://www.skysports.com/Images/skysports/site/ss-logo-07.gif</url>
            <link>http://www.skysports.com</link>
        </image>
        <ttl>120</ttl>

        <item>
            <title><![CDATA[Basel brush Valencia aside]]></title>
            <description><![CDATA[Matias Delgado scored a first-half brace as Basel convincingly beat Valencia 3-0 at home in the Europa League in a match which was played behind closed doors.]]></description>
            <link>http://www.skysports.com/football/match_report/0,19764,11065_3716124,00.html</link>
            <guid isPermaLink="false">11959_9247242</guid>
            <pubDate>Thu, 03 Apr 2014 22:06:04 GMT</pubDate>
            <category>Report</category>
            <enclosure type="image/jpg" url="http://img.skysports.com/14/04/128x67/Basel-v-Valencia-Matias-Delgado-second-goal-c_3113587.jpg" length="123456" />
        </item>

        <item>
            <title><![CDATA[AZ Alkmaar v Benfica preview]]></title>
            <description><![CDATA[Benfica will be hoping their excellent record against Dutch sides continues when they face AZ Alkmaar on Thursday night.]]></description>
            <link>http://www.skysports.com/football/match_preview/0,19764,11065_3716122,00.html</link>
            <guid isPermaLink="false">11959_9245377</guid>
            <pubDate>Wed, 02 Apr 2014 17:24:07 GMT</pubDate>
            <category>Preview</category>
            <enclosure type="image/jpg" url="http://img.skysports.com/14/03/128x67/dick-advocaat-az-alkmaar_3099409.jpg" length="123456" />
        </item>

        <item>
            <title><![CDATA[Basel face UEFA investigation]]></title>
            <description><![CDATA[UEFA has opened disciplinary proceedings against Basel after crowd trouble in their Europa League last 16 clash against Salzburg in Austria on Thursday night.]]></description>
            <link>http://www1.skysports.com/football/news/11959/9224972/europa-league-basel-face-uefa-investigation-following-red-bull-salzburg-match</link>
            <guid isPermaLink="false">11959_9224972</guid>
            <pubDate>Fri, 21 Mar 2014 16:26:19 GMT</pubDate>
            <category>News Story</category>
            <enclosure type="image/jpg" url="http://img.skysports.com/14/03/128x67/Red-Bull-Salzburg-v-Basel-Mohamed-Elneny-chal_3104602.jpg" length="123456" />
        </item>
    </channel>
</rss>
XML;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFetchFailsIfUrlDoesNotExist()
    {
        $badUrlMock = m::mock('Goutte\Client');
        $badUrlMock->shouldReceive('request')
            ->once()->with('GET', 'http:/some-server.nah/this/does/not/exist.made_up')
            ->andThrow('\InvalidArgumentException');
        $test = new Scraper('xml', 'http:/some-server.nah/this/does/not/exist.made_up');
        $test->setClient($badUrlMock);
        $test->fetch();
    }

    public function testEmptyChannelReturnsChannelData()
    {
        $emptyMock = m::mock('Goutte\Client');
        $emptyMock->shouldReceive('request')
            ->once()->with('GET', 'http://www.skysports.com/feed.xml')
            ->andReturn(new Crawler($this->emptyChannel));

        $test = new Scraper('xml', 'http://www.skysports.com/feed.xml');
        $test->setClient($emptyMock);
        $res = $test->fetch();
        $this->assertFeedMetadata($res);
        $this->assertEmpty($res->getItems(), 'Empty feed returned results');
    }
    public function testValidChannelReturnsCorrectItems()
    {
        $feedMock = m::mock('Goutte\Client');
        $feedMock->shouldReceive('request')
            ->once()->with('GET', 'http://www.skysports.com/feed.xml')
            ->andReturn(new Crawler($this->realChannel));

        $test = new Scraper('xml', 'http://www.skysports.com/feed.xml');
        $test->setClient($feedMock);
        $res = $test->fetch();
        $this->assertFeedMetadata($res);
        $items = $res->getItems();
        $this->assertEquals(3, count($items));
    }

    {
        $this->assertEquals('Sky Sports | Football', $res->getTitle(), 'Incorrect title');
        $this->assertEquals('http://www.skysports.com', $res->getLink(), 'Incorrect link');
        $this->assertEquals('Europa League News', $res->getDescription(), 'Incorrect description');
        $this->assertEquals('Football', $res->getCategory(), 'Incorrect category');
        $this->assertEquals(
            new \DateTime('Sat, 26 Apr 2014 15:20:18 GMT'),
            $res->getLastBuildDate(),
            'Incorrect build date'
        );
        $this->assertEquals('Sky Sports', $res->getImageTitle(), 'Incorrect image title');
        $this->assertEquals(
            'http://www.skysports.com/Images/skysports/site/ss-logo-07.gif',
            $res->getImageUrl(),
            'Incorrect image url'
        );
        $this->assertEquals('http://www.skysports.com', $res->getImageLink(), 'Incorrect image link');
    }
}
