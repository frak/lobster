<?php
/**
 * FeedRepositoryTest.php
 *
 * User: mikey
 * Date: 10/05/2014
 * Time: 18:09
 */

namespace Sport\LobsterBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Sport\LobsterBundle\Entity\Feed;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FeedRepositoryTest
 *
 * @todo At present this test relies on the feed data being present in the database, it seemed a bit overkill to introduce
 *       DoctrineFixtures for this as I am probably already way past the scope of your requirements ;o)
 *
 * @package Sport\LobsterBundle\Tests\Entity
 */
class FeedRepositoryTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindByTitleReturnsAllNewsItems()
    {
        /** @var Feed $feed */
        $feed = $this->em->getRepository('SportLobsterBundle:Feed')->findOneByTitle('Sky Sports | Football');

        $items = $feed->getItems();
        $this->assertEquals(18, count($items));
    }

    public function testFindByTitleAndCategoryOnlyReturnsNewsItemsOfThatCategory()
    {
        /** @var Feed $feed */
        $feed = $this->em->getRepository('SportLobsterBundle:Feed')->findOneByTitleAndItemCategory(
            'Sky Sports | Football',
            'Report'
        );

        $items = $feed->getItems();
        $this->assertEquals(10, count($items));
        foreach ($items as $item) {
            $this->assertEquals('Report', $item->getCategory(), 'Wrong item category');
        }
    }
}
