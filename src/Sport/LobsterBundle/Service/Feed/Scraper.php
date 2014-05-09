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
use Symfony\Component\DomCrawler\Crawler as SymfonyCrawler;
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
     * For mocking
     *
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Fetch the feed in the format specified
     *
     * @todo JSON implementation
     *
     * @return Feed
     * @throws \RuntimeException
     */
    public function fetch()
    {
        $crawler = $this->client->request('GET', $this->feedUrl);

        switch ($this->feedFormat) {
            case 'XML':
                $feed = $this->parseXml($crawler);
                break;
            default:
                throw new \RuntimeException("Unknown data format");
        }

        return $feed;
    }

    /**
     * @param SymfonyCrawler $crawler
     * @param string         $node
     *
     * @return mixed
     */
    private function getText(SymfonyCrawler $crawler, $node)
    {
        return trim($crawler->filterXPath($node)->text());
    }

    /**
     * @param SymfonyCrawler $crawler
     *
     * @return Feed
     * @throws \RuntimeException
     */
    private function parseXml(SymfonyCrawler $crawler)
    {
        try {
            $feed = new Feed();
            $feed->setTitle($this->getText($crawler, '//channel/title'))
                ->setLink($this->getText($crawler, '//channel/link'))
                ->setDescription($this->getText($crawler, '//channel/description'))
                ->setCategory($this->getText($crawler, '//channel/category'))
                ->setImageTitle($this->getText($crawler, '//channel/image/title'))
                ->setImageLink($this->getText($crawler, '//channel/image/link'))
                ->setImageUrl($this->getText($crawler, '//channel/image/url'))
                ->setLastBuildDate(new \DateTime($this->getText($crawler, '//channel/lastBuildDate')));

            $crawler->filterXPath('//channel/item')->each(
                function (SymfonyCrawler $node, $i) use ($feed) {
                    $item = new FeedItem();
                    /** @var $child \DOMElement */
                    foreach ($node->children() as $child) {
                        $nodeName = $child->nodeName;
                        switch ($nodeName) {
                            case 'enclosure':
                                $item->setEnclosure($child->getAttribute('url'));
                                break;
                            default:
                                $setter = 'set' . ucfirst($nodeName);
                                $item->{$setter}((string)$child->nodeValue);
                        }
                    }
                    $feed->addItem($item);
                }
            );
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException("{$this->feedUrl} returned a malformed response");
        }

        return $feed;
    }
}
