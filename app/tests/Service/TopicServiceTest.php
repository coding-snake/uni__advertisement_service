<?php

namespace App\Tests\Service;

use App\Entity\Topic;
use App\Repository\AdRepository;
use App\Repository\TopicRepository;
use App\Service\TopicService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TopicServiceTest extends TestCase
{
    private TopicRepository|MockObject $topicRepository;
    private AdRepository|MockObject $adRepository;
    private PaginatorInterface|MockObject $paginator;
    private TopicService $topicService;

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

    public function testGetPaginatedList(): void
    {
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
    }

    public function testSaveNewTopicSetsDates(): void
    {
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
    }

    public function testDelete(): void
    {
        // given
        $topic = new Topic();

        $this->topicRepository->expects($this->once())
            ->method('delete')
            ->with($topic);

        // when
        $this->topicService->delete($topic);
    }

    public function testCanBeDeletedReturnsTrueWhenNoAdsExist(): void
    {
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
    }

    public function testCanBeDeletedReturnsFalseWhenAdsExist(): void
    {
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
    }

    public function testCanBeDeletedReturnsFalseOnException(): void
    {
        // given
        $topic = new Topic();
        
        // Configure the mock to throw an exception when the repository method is called
        $this->adRepository->expects($this->once())
            ->method('countByTopic')
            ->willThrowException(new \Doctrine\ORM\NoResultException());

        // when
        $result = $this->topicService->canBeDeleted($topic);

        // then
        $this->assertFalse($result, 'Should return false when a NoResultException occurs.');
    }
}