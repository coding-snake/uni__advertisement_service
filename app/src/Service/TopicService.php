<?php

/**
 * Topic service.
 */

namespace App\Service;

use App\Entity\Topic;
use App\Repository\AdRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TopicService.
 */
class TopicService implements TopicServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param TopicRepository $topicRepository Topic repository
     * @param PaginatorInterface $paginator Paginator
     */
    public function __construct(private readonly TopicRepository $topicRepository, private readonly PaginatorInterface $paginator, private readonly AdRepository $adRepository)
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
            $this->topicRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['topic.id', 'topic.createdAt', 'topic.updatedAt', 'topic.name'],
                'defaultSortFieldName' => 'topic.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Topic $topic Topic entity
     */
    public function save(Topic $topic): void
    {
        $topic->setUpdatedAt(new \DateTimeImmutable());
        if (null === $topic->getId()) {
            $topic->setCreatedAt(new \DateTimeImmutable());
        }
        $this->topicRepository->save($topic);
    }

    /**
     * Delete entity.
     *
     * @param Topic $topic Topic entity
     */
    public function delete(Topic $topic): void
    {
        $this->topicRepository->delete($topic);
    }

    /**
     * Can Topic be deleted?
     *
     * @param Topic $topic Topic entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Topic $topic): bool
    {
        try {
            $result = $this->adRepository->countByTopic($topic);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
