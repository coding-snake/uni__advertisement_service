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
    private ?EntityManagerInterface $entity_manager;
    private ?TagRepository $tag_repository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->tag_repository = $this->entity_manager->getRepository(Tag::class);
    }

    /**
     * Test save.
     */
    public function test_save(): void
    {
        try {
            // given
            $tag = new Tag();
            $tag->setName('tag_name');

            // when
            $this->tag_repository->save($tag);

            // then
            $result = $this->tag_repository->find($tag->getId());

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
    public function test_delete(): void
    {
        try {
            // given
            $tag = new Tag();
            $tag->setName('tag_name');

            $this->entity_manager->persist($tag);
            $this->entity_manager->flush();

            $id = $tag->getId();

            // when
            $this->tag_repository->delete($tag);

            // then
            $this->assertNull(
                $this->tag_repository->find($id)
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
    public function test_query_all(): void
    {
        try {
            // given
            $tag = new Tag();
            $tag->setName('tag_name');

            $this->entity_manager->persist($tag);
            $this->entity_manager->flush();

            // when
            $result = $this->tag_repository
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