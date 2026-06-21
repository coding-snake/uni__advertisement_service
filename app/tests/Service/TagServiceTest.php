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
    private TagRepository|MockObject $tag_repository;
    private PaginatorInterface|MockObject $paginator;
    private TagService $tag_service;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        $this->tag_repository = $this->createMock(TagRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->tag_service = new TagService($this->tag_repository, $this->paginator);
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

            $this->tag_repository->expects($this->once())
                ->method('queryAll');

            $this->paginator->expects($this->once())
                ->method('paginate')
                ->willReturn($expected_result);

            // when
            $result = $this->tag_service->getPaginatedList($page);

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
     * Test save new tag.
     */
    public function test_save_new_tag_sets_dates(): void
    {
        try {
            // given
            $tag = new Tag();

            $this->tag_repository->expects($this->once())
                ->method('save')
                ->with($tag);

            // when
            $this->tag_service->save($tag);

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
    public function test_delete(): void
    {
        try {
            // given
            $tag = new Tag();

            $this->tag_repository->expects($this->once())
                ->method('delete')
                ->with($tag);

            // when
            $this->tag_service->delete($tag);

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