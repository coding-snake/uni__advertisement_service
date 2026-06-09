<?php

namespace App\Tests\Repository;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?TagRepository $tagRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagRepository = $this->entityManager->getRepository(Tag::class);
    }

    public function testSave(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $this->tagRepository->save($tag);

        $result = $this->tagRepository->find($tag->getId());

        $this->assertNotNull($result);
        $this->assertEquals('Test Tag', $result->getName());
    }

    public function testDelete(): void
    {
        $tag = new Tag();
        $tag->setName('Tag to delete');

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $id = $tag->getId();

        $this->tagRepository->delete($tag);

        $this->assertNull(
            $this->tagRepository->find($id)
        );
    }

    public function testQueryAll(): void
    {
        $tag = new Tag();
        $tag->setName('Unique Tag ' . uniqid());

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $result = $this->tagRepository
            ->queryAll()
            ->getQuery()
            ->getResult();

        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(Tag::class, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}