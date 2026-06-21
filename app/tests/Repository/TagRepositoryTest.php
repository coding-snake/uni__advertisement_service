<?php

/**
 * Tag repository tests.
 */

namespace App\Tests\Repository;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagRepositoryTest.
 */
class TagRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?TagRepository $tagRepository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagRepository = $this->entityManager->getRepository(Tag::class);
    }

    /**
     * Test save.
     */
    public function testSave(): void
    {
        try {
            // given
            $tag = new Tag();
            $tag->setName('tag_name');

            // when
            $this->tagRepository->save($tag);

            // then
            $result = $this->tagRepository->find($tag->getId());

            $this->assertNotNull($result);
            $this->assertEquals('tag_name', $result->getName());
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
            $tag->setName('tag_name');

            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $id = $tag->getId();

            // when
            $this->tagRepository->delete($tag);

            // then
            $this->assertNull(
                $this->tagRepository->find($id)
            );
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test query all.
     */
    public function testQueryAll(): void
    {
        try {
            // given
            $tag = new Tag();
            $tag->setName('tag_name');

            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            // when
            $result = $this->tagRepository
                ->queryAll()
                ->getQuery()
                ->getResult();

            // then
            $this->assertNotEmpty($result);
            $this->assertContainsOnlyInstancesOf(Tag::class, $result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
