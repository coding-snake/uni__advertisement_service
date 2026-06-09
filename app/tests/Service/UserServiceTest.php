<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserServiceTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private PaginatorInterface|MockObject $paginator;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->userService = new UserService($this->userRepository, $this->paginator);
    }

    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $expectedResult = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->userRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('u.id', 'DESC')
            ->willReturnSelf();

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with(
                $queryBuilder,
                $page,
                10,
                $this->callback(function ($argument) {
                    return is_array($argument);
                })
            )
            ->willReturn($expectedResult);

        // when
        $result = $this->userService->getPaginatedList($page);

        // then
        $this->assertSame($expectedResult, $result);
    }
}