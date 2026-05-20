<?php
/**
 * Tag service interface
 */

use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TagServiceInterface
 */
interface TagServiceInterface
{
    /**
     * Get paginated list
     *
     * @param int $page Page number
     *
     * @return PagionationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
}
