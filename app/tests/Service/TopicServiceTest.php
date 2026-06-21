<?php
/**
 * Topic service tests.
 */

namespace App\Tests\Service;

use App\Entity\Topic;
use App\Repository\AdRepository;
use App\Repository\TopicRepository;
use App\Service\TopicService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TopicServiceTest.
 */
class TopicServiceTest extends TestCase
{
    private TopicRepository|MockObject $topic_repository;
    private AdRepository|MockObject $ad_repository;
    private PaginatorInterface|MockObject $paginator;
    private TopicService $topic_service;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->topic_repository = $this->createMock(TopicRepository::class);
        $this->ad_repository = $this->createMock(AdRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->topic_service = new TopicService(
            $this->topic_repository,
            $this->paginator,
            $this->ad_repository
        );
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

            $this->topic_repository->expects($this->once())
                ->method('queryAll');

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->willReturn($expected_result);

            // when
            $result = $this->topic_service->getPaginatedList($page);

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
     * Test save new topic.
     */
    public function test_save_new_topic_sets_dates(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->topic_repository->expects($this->once())
                ->method('save')
                ->with($topic);

            // when
            $this->topic_service->save($topic);

            // then
            $this->assertNotNull($topic->getCreatedAt());
            $this->assertNotNull($topic->getUpdatedAt());
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
            $topic = new Topic();

            $this->topic_repository->expects($this->once())
                ->method('delete')
                ->with($topic);

            // when
            $this->topic_service->delete($topic);

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
     * Test can be deleted true.
     */
    public function test_can_be_deleted_returns_true_when_no_ads_exist(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->ad_repository->expects($this->once())
                ->method('countByTopic')
                ->with($topic)
                ->willReturn(0);

            // when
            $result = $this->topic_service->canBeDeleted($topic);

            // then
            $this->assertTrue($result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test can be deleted false.
     */
    public function test_can_be_deleted_returns_false_when_ads_exist(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->ad_repository->expects($this->once())
                ->method('countByTopic')
                ->with($topic)
                ->willReturn(5);

            // when
            $result = $this->topic_service->canBeDeleted($topic);

            // then
            $this->assertFalse($result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test can be deleted exception.
     */
    public function test_can_be_deleted_returns_false_on_exception(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->ad_repository->expects($this->once())
                ->method('countByTopic')
                ->willThrowException(new \Doctrine\ORM\NoResultException());

            // when
            $result = $this->topic_service->canBeDeleted($topic);

            // then
            $this->assertFalse($result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}