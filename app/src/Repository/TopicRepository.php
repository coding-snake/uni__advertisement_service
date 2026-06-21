<?php

/**
 * Topic Repository.
 */

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 */
class TopicRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Topic::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('topic');
    }

    /**
     * Save entity.
     *
     * @param Topic $topic Topic entity
     */
    public function save(Topic $topic): void
    {
        $this->getEntityManager()->persist($topic);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Topic $topic Topic entity
     */
    public function delete(Topic $topic): void
    {
        $this->getEntityManager()->remove($topic);
        $this->getEntityManager()->flush();
    }
}
