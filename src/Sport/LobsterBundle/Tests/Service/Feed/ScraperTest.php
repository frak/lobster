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
    /**
     * @expectedException \RuntimeException
     */
    public function testInstantiationFailsIfUrlDoesNotExist()
    {
        $test = new Scraper('xml', 'file:///this/does/not/exist.made_up');
    }

    public function testEmptyChannelReturnsEmptyList()
    {
        $path = realpath(dirname(__FILE__) . '/emptyChannel.xml');
        $test = new Scraper('xml', $path);
    }
}
