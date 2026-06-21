<?php
/**
 * Ad service tests.
 */

namespace App\Tests\Service;

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
    private AdRepository|MockObject $ad_repository;
    private PaginatorInterface|MockObject $paginator;
    private AdService $ad_service;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->ad_repository = $this->createMock(AdRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->ad_service = new AdService($this->ad_repository, $this->paginator);
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

            $this->ad_repository->expects($this->once())
                ->method('queryAll');

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->willReturn($expected_result);

            // when
            $result = $this->ad_service->getPaginatedList($page);

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

    /**
     * Test save new ad.
     */
    public function test_save_new_ad_sets_dates(): void
    {
        try {
            // given
            $ad = new Ad();

            $this->ad_repository->expects($this->once())
                ->method('save')
                ->with($ad);

            // when
            $this->ad_service->save($ad);

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
    public function test_delete(): void
    {
        try {
            // given
            $ad = new Ad();

            $this->ad_repository->expects($this->once())
                ->method('delete')
                ->with($ad);

            // when
            $this->ad_service->delete($ad);

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
    public function test_get_paginated_list_by_topic(): void
    {
        try {
            // given
            $topic = new Topic();
            $page = 1;
            $expected_result = $this->createMock(PaginationInterface::class);
            $query_builder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);

            $this->ad_repository->expects($this->once())
                ->method('createQueryBuilder')
                ->willReturn($query_builder);

            $query_builder->method('where')->willReturnSelf();
            $query_builder->method('setParameter')->willReturnSelf();
            $query_builder->method('orderBy')->willReturnSelf();

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->with($query_builder, $page, 10)
                ->willReturn($expected_result);

            // when
            $result = $this->ad_service->getPaginatedListByTopic($topic, $page);

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

    /**
     * Test get paginated list by tag.
     */
    public function test_get_paginated_list_by_tag(): void
    {
        try {
            // given
            $tag = new Tag();
            $page = 1;
            $expected_result = $this->createMock(PaginationInterface::class);
            $query_builder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);

            $this->ad_repository->expects($this->once())
                ->method('createQueryBuilder')
                ->willReturn($query_builder);

            $query_builder->method('innerJoin')->willReturnSelf();
            $query_builder->method('where')->willReturnSelf();
            $query_builder->method('setParameter')->willReturnSelf();
            $query_builder->method('orderBy')->willReturnSelf();

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->with($query_builder, $page, 10)
                ->willReturn($expected_result);

            // when
            $result = $this->ad_service->getPaginatedListByTag($tag, $page);

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