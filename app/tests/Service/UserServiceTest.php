<?php
/**
 * User service tests.
 */

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UserServiceTest.
 */
class UserServiceTest extends TestCase
{
    private UserRepository|MockObject $user_repository;
    private PaginatorInterface|MockObject $paginator;
    private UserService $user_service;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->user_repository = $this->createMock(UserRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->user_service = new UserService($this->user_repository, $this->paginator);
    }

    /**
     * Test get paginated list.
     */
    public function test_get_paginated_list(): void
    {
        try {
            // given
            $page = 1;
            $expected_result = $this->createMock(PaginationInterface::class);
            $query_builder = $this->createMock(QueryBuilder::class);

            $this->user_repository->expects($this->once())
                ->method('createQueryBuilder')
                ->with('u')
                ->willReturn($query_builder);

            $query_builder->expects($this->once())
                ->method('orderBy')
                ->with('u.id', 'DESC')
                ->willReturnSelf();

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->with(
                    $query_builder,
                    $page,
                    10,
                    $this->callback(function ($argument) {
                        return is_array($argument);
                    })
                )
                ->willReturn($expected_result);

            // when
            $result = $this->user_service->getPaginatedList($page);

            // then
            $this->assertSame($expected_result, $result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}