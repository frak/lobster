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
     * @Route("/football/{category}", name="football_news", defaults={"category" = ""})
     * @Template()
     */
    public function footballNewsAction($category)
    {
        $category = ucfirst($category);
        $repo     = $this->get('doctrine.orm.entity_manager')->getRepository('SportLobsterBundle:Feed');
        if (empty($category)) {
            $feed = $repo->findOneByTitle('Sky Sports | Football');
        } else {
            $feed = $repo->findOneByTitleAndItemCategory('Sky Sports | Football', $category);
        }

        return compact('feed');
    }
}
