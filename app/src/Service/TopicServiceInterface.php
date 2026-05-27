<?php
/**
 * Topic service interface.
 */

namespace App\Service;

use App\Entity\Topic;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TaskServiceInterface.
 */
interface TopicServiceInterface
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
     * Save entity.
     *
     * @param Topic $topic Topic entity
     */
    public function save(Topic $topic): void;

    /**
     * Delete entity.
     *
     * @param Topic $topic Topic entity
     */
    public function delete(Topic $topic): void;

    /**
     * Can Topic be deleted?
     *
     * @param Topic $topic Topic entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Topic $topic): bool;
}
