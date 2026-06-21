<?php

/**
 * Topic service tests.
 */

namespace App\Tests\Service;

use Doctrine\ORM\NoResultException;
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
    private TopicRepository|MockObject $topicRepository;
    private AdRepository|MockObject $adRepository;
    private PaginatorInterface|MockObject $paginator;
    private TopicService $topicService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->topicRepository = $this->createMock(TopicRepository::class);
        $this->adRepository = $this->createMock(AdRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->topicService = new TopicService(
            $this->topicRepository,
            $this->paginator,
            $this->adRepository
        );
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

            $this->topicRepository->expects($this->once())
                ->method('queryAll');

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->willReturn($expectedResult);

            // when
            $result = $this->topicService->getPaginatedList($page);

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
     * Test save new topic.
     */
    public function testSaveNewTopicSetsDates(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->topicRepository->expects($this->once())
                ->method('save')
                ->with($topic);

            // when
            $this->topicService->save($topic);

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
    public function testDelete(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->topicRepository->expects($this->once())
                ->method('delete')
                ->with($topic);

            // when
            $this->topicService->delete($topic);

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
    public function testCanBeDeletedReturnsTrueWhenNoAdsExist(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->adRepository->expects($this->once())
                ->method('countByTopic')
                ->with($topic)
                ->willReturn(0);

            // when
            $result = $this->topicService->canBeDeleted($topic);

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
    public function testCanBeDeletedReturnsFalseWhenAdsExist(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->adRepository->expects($this->once())
                ->method('countByTopic')
                ->with($topic)
                ->willReturn(5);

            // when
            $result = $this->topicService->canBeDeleted($topic);

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
    public function testCanBeDeletedReturnsFalseOnException(): void
    {
        try {
            // given
            $topic = new Topic();

            $this->adRepository->expects($this->once())
                ->method('countByTopic')
                ->willThrowException(new NoResultException());

            // when
            $result = $this->topicService->canBeDeleted($topic);

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
