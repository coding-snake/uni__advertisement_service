<?php

/**
 * Ad Repository.
 */

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ad>
 */
class AdRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @var int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('ad')
        ->select('ad', 'topic', 'tags')
        ->join('ad.topic', 'topic')
        ->leftJoin('ad.tags', 'tags');
    }

    /**
     * Save entity.
     *
     * @param Ad $ad Ad entity
     */
    public function save(Ad $ad): void
    {
        $this->getEntityManager()->persist($ad);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Ad $ad Ad entity
     */
    public function delete(Ad $ad): void
    {
        $this->getEntityManager()->remove($ad);
        $this->getEntityManager()->flush();
    }

    /**
     * Count ads by topic.
     *
     * @param Topic $topic Topic
     *
     * @return int Number of ads in topic
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByTopic(Topic $topic): int
    {
        $qb = $this->createQueryBuilder('ad');

        return $qb->select($qb->expr()->countDistinct('ad.id'))
            ->where('ad.topic = :topic')
            ->setParameter(':topic', $topic)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
