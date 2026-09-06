<?php

/**
 * Ad service interface.
 */

namespace App\Service;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface AdServiceInterface.
 */
interface AdServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of ads per topic.
     *
     * @param Topic $topic Topic entity
     * @param int   $page  Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByTopic(Topic $topic, int $page): PaginationInterface;

    /**
     * Get paginated list of ads per tag.
     *
     * @param Tag $tag  Tag entity
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByTag(Tag $tag, int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Ad $ad Ad entity
     */
    public function save(Ad $ad): void;

    /**
     * Delete entity.
     *
     * @param Ad $ad Ad entity
     */
    public function delete(Ad $ad): void;

    /**
     * Toggle verification status for an ad.
     *
     * @param Ad $ad Ad entity
     */
    public function toggleVerification(Ad $ad): void;
}
