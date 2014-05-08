<?php
/**
 * Scraper.php
 *
 * User: mikey
 * Date: 08/05/2014
 * Time: 19:21
 */

namespace Sport\LobsterBundle\Service\Feed;

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

    public function __construct($feedFormat, $feedUrl)
    {
        $this->feedFormat = $feedFormat;
        $this->feedUrl    = $feedUrl;
        if(!file_exists($feedUrl)) {
            throw new \RuntimeException("{$feedUrl} does not exist");
        }
    }
}
