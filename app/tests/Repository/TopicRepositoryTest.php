<?php
/**
 * Topic repository tests.
 */

namespace App\Tests\Repository;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TopicRepositoryTest.
 */
class TopicRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entity_manager;
    private ?TopicRepository $topic_repository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->topic_repository = $this->entity_manager->getRepository(Topic::class);
    }

    /**
     * Test save.
     */
    public function test_save(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            // when
            $this->topic_repository->save($topic);

            // then
            $result = $this->topic_repository->find($topic->getId());

            $this->assertNotNull($result);
            $this->assertEquals('topic_name', $result->getName());
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
            $topic->setName('topic_name');

            $this->entity_manager->persist($topic);
            $this->entity_manager->flush();

            $id = $topic->getId();

            // when
            $this->topic_repository->delete($topic);

            // then
            $this->assertNull(
                $this->topic_repository->find($id)
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
            $topic = new Topic();
            $topic->setName('topic_name');

            $this->entity_manager->persist($topic);
            $this->entity_manager->flush();

            // when
            $result = $this->topic_repository
                ->queryAll()
                ->getQuery()
                ->getResult();

            // then
            $this->assertNotEmpty($result);
            $this->assertContainsOnlyInstancesOf(Topic::class, $result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}