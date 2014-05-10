<?php
/**
 * ScrapeCommand.php
 *
 * User: mikey
 * Date: 10/05/2014
 * Time: 17:20
 */

namespace Sport\LobsterBundle\Command;

use Sport\LobsterBundle\Entity\Feed;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ScrapeCommand
 *
 * @package Sport\LobsterBundle\Command
 */
class ScrapeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('lobster:sky:scraper')
            ->setDescription('Scrape the Sky Football RSS feed and persist news items');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $output->writeln('Fetching feed from server');
        $newFeed = $this->getContainer()->get('lobster.sky.scraper')->fetch();
        $oldFeed = $em->getRepository('SportLobsterBundle:Feed')->findOneBy(array('title' => $newFeed->getTitle()));
        if ($oldFeed) {
            $output->writeln('Removing old entity');
            $em->remove($oldFeed);
            $em->flush();
        }
        $output->writeln('Persisting new entity');
        $em->persist($newFeed);
        $em->flush();
        $output->writeln('<info>Done</info>');
    }
}
