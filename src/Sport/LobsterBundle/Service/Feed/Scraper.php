<?php
/**
 * Scraper.php
 *
 * User: mikey
 * Date: 08/05/2014
 * Time: 19:21
 */

namespace Sport\LobsterBundle\Service\Feed;

use Goutte\Client;
use Sport\LobsterBundle\Entity\Feed;
use Sport\LobsterBundle\Entity\FeedItem;

/**
 * Class Scraper
 *
 * @package Sport\LobsterBundle\Service\Feed
 */
class Scraper
{
    /**
     * @var string
     */
    private $feedUrl;

    /**
     * @var string
     */
    private $feedFormat;

    /**
     * @var Client
     */
    private $client;

    /**
     * Check the feed exists before instantiation
     *
     * @param string $feedFormat
     * @param string $feedUrl
     */
    public function __construct($feedFormat, $feedUrl)
    {
        $this->feedFormat = $feedFormat;
        $this->feedUrl    = $feedUrl;
        $this->client     = new Client();
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    public function fetch()
    {
        $crawler = $this->client->request('GET', $this->feedUrl);

        $feed    = new Feed();
        $feed->setTitle($this->getText($crawler, '//channel/title'))
            ->setLink($this->getText($crawler, '//channel/link'))
            ->setDescription($this->getText($crawler, '//channel/description'))
            ->setCategory($this->getText($crawler, '//channel/category'))
            ->setImageTitle($this->getText($crawler, '//channel/image/title'))
            ->setImageLink($this->getText($crawler, '//channel/image/link'))
            ->setImageUrl($this->getText($crawler, '//channel/image/url'))
            ->setLastBuildDate(new \DateTime($this->getText($crawler, '//channel/lastBuildDate')));

        $crawler->filterXPath('//channel/item')->each(
            function (\Symfony\Component\DomCrawler\Crawler $node, $i) {
                foreach($node->children() as $child) {
                    var_dump($child->nodeName);
                }
            }
        );

        return $feed;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string                               $node
     *
     * @return mixed
     */
    private function getText(\Symfony\Component\DomCrawler\Crawler $crawler, $node)
    {
        return trim($crawler->filterXPath($node)->text());
    }
}
