<?php
/**
 * ScrapeCommandTest.php
 *
 * User: mikey
 * Date: 10/05/2014
 * Time: 18:26
 */

namespace Sport\LobsterBundle\Tests\Command;

require_once(__DIR__ . '/../../../../../app/AppKernel.php');

use Sport\LobsterBundle\Command\ScrapeCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ScrapeCommandTest
 *
 * @todo Again this test relies on the database being pre-loaded, sorry
 *
 * @package Sport\LobsterBundle\Tests\Command
 */
class ScrapeCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {

        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ScrapeCommand());

        $command = $application->find('lobster:sky:scraper');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $display = $commandTester->getDisplay();
        $this->assertRegExp('/Fetching feed from server/', $display);
        $this->assertRegExp('/Removing old entity/', $display);
        $this->assertRegExp('/Persisting new entity/', $display);
        $this->assertRegExp('/Done/', $display);
    }
}
