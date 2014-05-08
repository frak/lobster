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

/**
 * Class ScraperTest
 *
 * @package Sport\LobsterBundle\Tests\Service\Feed
 */
class ScraperTest extends \PHPUnit_Framework_TestCase
{
    private $testClass;

    public function setUp()
    {
        $this->testClass = new Scraper('xml', 'file:///var/www/sites/sportlobster.dev/feed.xml');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInstantiationFailsIfUrlDoesNotExist()
    {
        $badUrlClass = new Scraper('xml', 'file:///this/does/not/exist.made_up');
    }
}
