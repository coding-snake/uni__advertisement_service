<?php

namespace App\Tests\Repository;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TopicRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?TopicRepository $topicRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->topicRepository = $this->entityManager->getRepository(Topic::class);
    }

    public function testSave(): void
    {
        $topic = new Topic();
        $topic->setName('Test Topic');

        $this->topicRepository->save($topic);

        $result = $this->topicRepository->find($topic->getId());

        $this->assertNotNull($result);
        $this->assertEquals('Test Topic', $result->getName());
    }

    public function testDelete(): void
    {
        $topic = new Topic();
        $topic->setName('Topic to delete');

        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $id = $topic->getId();

        $this->topicRepository->delete($topic);

        $this->assertNull(
            $this->topicRepository->find($id)
        );
    }

    public function testQueryAll(): void
    {
        $topic = new Topic();
        $topic->setName('New Topic');

        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $result = $this->topicRepository
            ->queryAll()
            ->getQuery()
            ->getResult();

        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(Topic::class, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->entityManager->close();
        $this->entityManager = null;
    }
}