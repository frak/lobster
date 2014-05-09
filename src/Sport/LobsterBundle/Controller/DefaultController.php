<?php

namespace Sport\LobsterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_page")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/football", name="football_news")
     * @Template()
     */
    public function footballNewsAction()
    {
        try {
            /**
             * This is only here to get around not having a real server to get the feed from
             */
            $feedContents = file_get_contents($this->container->getParameter('feed.xml'));
            $feed = $this->get('lobster.sky.scraper')->fetch($feedContents);
        } catch (\RuntimeException $e) {
            $feed = null;
        }

        return compact('feed');
    }
}
