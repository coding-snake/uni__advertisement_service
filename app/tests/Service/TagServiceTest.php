<?php

/**
 * Tag service tests.
 */

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\TagService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TagServiceTest.
 */
class TagServiceTest extends TestCase
{
    private TagRepository|MockObject $tagRepository;
    private PaginatorInterface|MockObject $paginator;
    private TagService $tagService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->tagRepository = $this->createMock(TagRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->tagService = new TagService($this->tagRepository, $this->paginator);
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

            $this->tagRepository->expects($this->once())
                ->method('queryAll');

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->willReturn($expectedResult);

            // when
            $result = $this->tagService->getPaginatedList($page);

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
     * Test save new tag.
     */
    public function testSaveNewTagSetsDates(): void
    {
        try {
            // given
            $tag = new Tag();

            $this->tagRepository->expects($this->once())
                ->method('save')
                ->with($tag);

            // when
            $this->tagService->save($tag);

            // then
            $this->assertNotNull($tag->getCreatedAt());
            $this->assertNotNull($tag->getUpdatedAt());
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
            $tag = new Tag();

            $this->tagRepository->expects($this->once())
                ->method('delete')
                ->with($tag);

            // when
            $this->tagService->delete($tag);

            // then
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
