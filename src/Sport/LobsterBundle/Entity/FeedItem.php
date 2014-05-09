<?php

namespace Sport\LobsterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeedItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sport\LobsterBundle\Entity\FeedItemRepository")
 */
class FeedItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=255)
     */
    private $guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pubDate", type="datetime")
     */
    private $pubDate;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="enclosure", type="string", length=255)
     */
    private $enclosure;

    /**
     * @var Feed
     *
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="items")
     */
    private $feed;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return FeedItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return FeedItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return FeedItem
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set guid
     *
     * @param string $guid
     *
     * @return FeedItem
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set pubDate
     *
     * @param \DateTime $pubDate
     *
     * @return FeedItem
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    /**
     * Get pubDate
     *
     * @return \DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return FeedItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set enclosure
     *
     * @param string $enclosure
     *
     * @return FeedItem
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Get enclosure
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Set feed
     *
     * @param \Sport\LobsterBundle\Entity\Feed $feed
     *
     * @return FeedItem
     */
    public function setFeed(\Sport\LobsterBundle\Entity\Feed $feed = null)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * Get feed
     *
     * @return \Sport\LobsterBundle\Entity\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }
}
