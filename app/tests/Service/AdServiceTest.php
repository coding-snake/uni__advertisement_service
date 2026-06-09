<?php

namespace App\Tests\Service;

use App\Entity\Ad;
use App\Entity\Topic;
use App\Repository\AdRepository;
use App\Service\AdService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AdServiceTest extends TestCase
{
    private AdRepository|MockObject $adRepository;
    private PaginatorInterface|MockObject $paginator;
    private AdService $adService;

    protected function setUp(): void
    {
        $this->adRepository = $this->createMock(AdRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->adService = new AdService($this->adRepository, $this->paginator);
    }

    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $expectedResult = $this->createMock(PaginationInterface::class);
        
        $this->adRepository->expects($this->once())
            ->method('queryAll');
            
        $this->paginator->expects($this->once())
            ->method('paginate')
            ->willReturn($expectedResult);

        // when
        $result = $this->adService->getPaginatedList($page);

        // then
        $this->assertSame($expectedResult, $result);
    }

    public function testSaveNewAdSetsDates(): void
    {
        // given
        $ad = new Ad();
        
        $this->adRepository->expects($this->once())
            ->method('save')
            ->with($ad);

        // when
        $this->adService->save($ad);

        // then
        $this->assertNotNull($ad->getCreatedAt());
        $this->assertNotNull($ad->getUpdatedAt());
    }

    public function testDelete(): void
    {
        // given
        $ad = new Ad();
        
        $this->adRepository->expects($this->once())
            ->method('delete')
            ->with($ad);
        // when
        // then
        $this->adService->delete($ad);
    }

    public function testGetPaginatedListByTopic(): void
    {
        // given
        $topic = new Topic();
        $page = 1;
        $expectedResult = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);

        // We need to mock the chain of calls for the QueryBuilder
        $this->adRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('orderBy')->willReturnSelf();

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with($queryBuilder, $page, 10)
            ->willReturn($expectedResult);

        // when
        $result = $this->adService->getPaginatedListByTopic($topic, $page);

        // then
        $this->assertSame($expectedResult, $result);
    }

    public function testGetPaginatedListByTag(): void
    {
        // given
        $tag = new \App\Entity\Tag();
        $page = 1;
        $expectedResult = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);

        $this->adRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder->method('innerJoin')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('orderBy')->willReturnSelf();

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with($queryBuilder, $page, 10)
            ->willReturn($expectedResult);

        // when
        $result = $this->adService->getPaginatedListByTag($tag, $page);

        // then
        $this->assertSame($expectedResult, $result);
    }
}