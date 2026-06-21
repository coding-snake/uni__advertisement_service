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
    private ?EntityManagerInterface $entityManager;
    private ?TopicRepository $topicRepository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->topicRepository = $this->entityManager->getRepository(Topic::class);
    }

    /**
     * Test save.
     */
    public function testSave(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            // when
            $this->topicRepository->save($topic);

            // then
            $result = $this->topicRepository->find($topic->getId());

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
    public function testDelete(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $this->entityManager->persist($topic);
            $this->entityManager->flush();

            $id = $topic->getId();

            // when
            $this->topicRepository->delete($topic);

            // then
            $this->assertNull(
                $this->topicRepository->find($id)
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
            $topic = new Topic();
            $topic->setName('topic_name');

            $this->entityManager->persist($topic);
            $this->entityManager->flush();

            // when
            $result = $this->topicRepository
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
