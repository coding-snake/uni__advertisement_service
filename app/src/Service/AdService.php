<?php

/**
 * Ad service.
 */

namespace App\Service;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AdService
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
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param AdRepository     $adRepository Ad repository
     * @param PaginatorInterface $paginator      Paginator
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
