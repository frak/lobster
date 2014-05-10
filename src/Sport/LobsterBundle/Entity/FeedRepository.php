<?php

namespace Sport\LobsterBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class FeedRepository
 *
 * @package Sport\LobsterBundle\Entity
 */
class FeedRepository extends EntityRepository
{
    public function findOneByTitle($title)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f', 'fi')
            ->join('f.items', 'fi')
            ->where('f.title = :title')
            ->setParameter('title', $title);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Find feed by title and the news item category
     *
     * @param string $title
     * @param string $category
     *
     * @return mixed
     */
    public function findOneByTitleAndItemCategory($title, $category)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f', 'fi')
            ->join('f.items', 'fi')
            ->where('f.title = :title')
            ->andWhere('fi.category = :category')
            ->setParameter('title', $title)
            ->setParameter('category', $category);

        return $qb->getQuery()->getSingleResult();
    }
}
