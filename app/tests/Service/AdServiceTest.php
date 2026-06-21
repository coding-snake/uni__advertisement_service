<?php

/**
 * Ad service tests.
 */

namespace App\Tests\Service;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Repository\AdRepository;
use App\Service\AdService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AdServiceTest.
 */
class AdServiceTest extends TestCase
{
    private AdRepository|MockObject $adRepository;
    private PaginatorInterface|MockObject $paginator;
    private AdService $adService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->adRepository = $this->createMock(AdRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->adService = new AdService($this->adRepository, $this->paginator);
    }

    /**
     * Test get paginated list.
     */
    public function testGetPaginatedList(): void
    {
        try {
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
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test save new ad.
     */
    public function testSaveNewAdSetsDates(): void
    {
        try {
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
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test delete.
     */
    public function testDelete(): void
    {
        try {
            // given
            $ad = new Ad();

            $this->adRepository->expects($this->once())
                ->method('delete')
                ->with($ad);

            // when
            $this->adService->delete($ad);

            // then
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test get paginated list by topic.
     */
    public function testGetPaginatedListByTopic(): void
    {
        try {
            // given
            $topic = new Topic();
            $page = 1;
            $expectedResult = $this->createMock(PaginationInterface::class);
            $queryBuilder = $this->createMock(QueryBuilder::class);

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
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test get paginated list by tag.
     */
    public function testGetPaginatedListByTag(): void
    {
        try {
            // given
            $tag = new Tag();
            $page = 1;
            $expectedResult = $this->createMock(PaginationInterface::class);
            $queryBuilder = $this->createMock(QueryBuilder::class);

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
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
