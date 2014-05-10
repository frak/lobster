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
        $this->feedFormat = strtolower($feedFormat);
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
     * @param string $category
     * @param string $response
     *
     * @throws \RuntimeException
     * @return Feed
     */
    public function fetch($category = '', $response = '')
    {
        if (empty($response)) {
            $crawler = $this->client->request('GET', $this->feedUrl);
        } else {
            $crawler = new SymfonyCrawler($response);
        }

        switch ($this->feedFormat) {
            case 'xml':
                $feed = $this->parseXml($crawler, $category);
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
     * @param                $category
     *
     * @throws \RuntimeException
     * @return Feed
     */
    private function parseXml(SymfonyCrawler $crawler, $category)
    {
        try {
            $feed = new Feed();
            $feed->setTitle($this->getText($crawler, '//channel/title'))
                ->setCategory($this->getText($crawler, '//channel/category'))
                ->setLink($this->getText($crawler, '//channel/link'))
                ->setDescription($this->getText($crawler, '//channel/description'))
                ->setImageTitle($this->getText($crawler, '//channel/image/title'))
                ->setImageLink($this->getText($crawler, '//channel/image/link'))
                ->setImageUrl($this->getText($crawler, '//channel/image/url'))
                ->setLastBuildDate(new \DateTime($this->getText($crawler, '//channel/lastBuildDate')));

            $crawler->filterXPath('//channel/item')->each(
                function (SymfonyCrawler $node, $i) use ($feed, $category) {
                    $item         = new FeedItem();
                    $thisCategory = null;
                    /** @var $child \DOMElement */
                    foreach ($node->children() as $child) {
                        $nodeName = $child->nodeName;
                        switch ($nodeName) {
                            case 'enclosure':
                                $item->setEnclosure($child->getAttribute('url'));
                                break;
                            case 'category':
                                $thisCategory = (string)$child->nodeValue;
                            default:
                                $setter = 'set' . ucfirst($nodeName);
                                $item->{$setter}((string)$child->nodeValue);
                                break;
                        }
                    }
                    if ($thisCategory !== 'Preview') {
                        if (empty($category) || (!empty($category) && $thisCategory === $category)) {
                            $feed->addItem($item);
                        }
                    }
                }
            );
        } catch (\InvalidArgumentException $e) {
            $thrown = new \RuntimeException("{$this->feedUrl} returned a malformed response", 999, $e);
            throw $thrown;
        }

        return $feed;
    }
}
