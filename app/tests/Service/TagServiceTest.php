<?php

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\TagService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TagServiceTest extends TestCase
{
    private TagRepository|MockObject $tagRepository;
    private PaginatorInterface|MockObject $paginator;
    private TagService $tagService;

    protected function setUp(): void
    {
        $this->tagRepository = $this->createMock(TagRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->tagService = new TagService($this->tagRepository, $this->paginator);
    }

    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $expectedResult = $this->createMock(PaginationInterface::class);

        $this->tagRepository->expects($this->once())
            ->method('queryAll');

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->willReturn($expectedResult);

        // when
        $result = $this->tagService->getPaginatedList($page);

        // then
        $this->assertSame($expectedResult, $result);
    }

    public function testSaveNewTagSetsDates(): void
    {
        // given
        $tag = new Tag();

        $this->tagRepository->expects($this->once())
            ->method('save')
            ->with($tag);

        // when
        $this->tagService->save($tag);

        // then
        $this->assertNotNull($tag->getCreatedAt(), 'Created date should be set.');
        $this->assertNotNull($tag->getUpdatedAt(), 'Updated date should be set.');
    }

    public function testDelete(): void
    {
        // given
        $tag = new Tag();

        $this->tagRepository->expects($this->once())
            ->method('delete')
            ->with($tag);

        // when
        $this->tagService->delete($tag);

        // then=
    }
}