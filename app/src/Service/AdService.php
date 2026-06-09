<?php

/**
 * Ad service.
 */

namespace App\Service;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Repository\AdRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AdService.
 */
class AdService implements AdServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @varant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param AdRepository       $adRepository Ad repository
     * @param PaginatorInterface $paginator    Paginator
     */
    public function __construct(private readonly AdRepository $adRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->adRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['ad.id', 'ad.createdAt', 'ad.updatedAt', 'ad.title', 'topic.name'],
                'defaultSortFieldName' => 'ad.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Get paginated list by topic.
     *
     * @param Topic $topic Topic entity
     * @param int   $page  Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByTopic(Topic $topic, int $page): PaginationInterface
    {
        $queryBuilder = $this->adRepository->createQueryBuilder('ad')
            ->where('ad.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy('ad.createdAt', 'DESC');

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            10
        );
    }

    /**
     * Get paginated list by tag.
     *
     * @param Tag $tag  Tag entity
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByTag(Tag $tag, int $page): PaginationInterface
    {
        $queryBuilder = $this->adRepository->createQueryBuilder('ad')
            ->innerJoin('ad.tags', 'tag')
            ->where('tag = :tag')
            ->setParameter('tag', $tag)
            ->orderBy('ad.createdAt', 'DESC');

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            10
        );
    }

    /**
     * Save entity.
     *
     * @param Ad $ad Ad entity
     */
    public function save(Ad $ad): void
    {
        $ad->setUpdatedAt(new \DateTimeImmutable());
        if (null === $ad->getId()) {
            $ad->setCreatedAt(new \DateTimeImmutable());
        }
        $this->adRepository->save($ad);
    }

    /**
     * Delete entity.
     *
     * @param Ad $ad Ad entity
     */
    public function delete(Ad $ad): void
    {
        $this->adRepository->delete($ad);
    }
}
