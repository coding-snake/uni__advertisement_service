<?php

namespace App\Tests\Repository;

use App\Entity\Ad;
use App\Entity\Topic;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?AdRepository $adRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->adRepository = $this->entityManager->getRepository(Ad::class);
    }

    public function testSave(): void
{
    $topic = new Topic();
    $topic->setName('Test topic');

    $this->entityManager->persist($topic);

    $ad = new Ad();
    $ad->setName('Test ad');
    $ad->setContent('Test content');
    $ad->setTopic($topic);

    $this->adRepository->save($ad);

    $result = $this->adRepository->find($ad->getId());

    $this->assertNotNull($result);
    $this->assertEquals('Test ad', $result->getName());
}

public function testDelete(): void
{
    $topic = new Topic();
    $topic->setName('Test topic');

    $ad = new Ad();
    $ad->setName('Test ad');
    $ad->setContent('Test content');
    $ad->setTopic($topic);

    $this->entityManager->persist($topic);
    $this->entityManager->persist($ad);
    $this->entityManager->flush();

    $id = $ad->getId();

    $this->adRepository->delete($ad);

    $this->assertNull(
        $this->adRepository->find($id)
    );
}

public function testCountByTopic(): void
{
    $topic = new Topic();
    $topic->setName('Symfony');

    $this->entityManager->persist($topic);

    for ($i = 0; $i < 3; ++$i) {
        $ad = new Ad();
        $ad->setName('Ad '.$i);
        $ad->setContent('Content '.$i);
        $ad->setTopic($topic);

        $this->entityManager->persist($ad);
    }

    $this->entityManager->flush();

    $result = $this->adRepository->countByTopic($topic);

    $this->assertEquals(3, $result);
}

public function testQueryAll(): void
{
    $topic = new Topic();
    $topic->setName('Symfony');

    $ad = new Ad();
    $ad->setName('Test ad');
    $ad->setContent('Content');
    $ad->setTopic($topic);

    $this->entityManager->persist($topic);
    $this->entityManager->persist($ad);
    $this->entityManager->flush();

    $result = $this->adRepository
        ->queryAll()
        ->getQuery()
        ->getResult();

    $this->assertNotEmpty($result);
    $this->assertContainsOnlyInstancesOf(Ad::class, $result);
}
}