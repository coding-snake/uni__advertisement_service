<?php

/**
 * Ad repository tests.
 */

namespace App\Tests\Repository;

use App\Entity\Ad;
use App\Entity\Topic;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class AdRepositoryTest.
 */
class AdRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?AdRepository $adRepository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->adRepository = $this->entityManager->getRepository(Ad::class);
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

            $this->entityManager->persist($topic);

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            // when
            $this->adRepository->save($ad);

            // then
            $result = $this->adRepository->find($ad->getId());

            $this->assertNotNull($result);
            $this->assertEquals('ad_name', $result->getName());
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

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            $this->entityManager->persist($topic);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();

            $id = $ad->getId();

            // when
            $this->adRepository->delete($ad);

            // then
            $this->assertNull(
                $this->adRepository->find($id)
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
     * Test count by topic.
     */
    public function testCountByTopic(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $this->entityManager->persist($topic);

            for ($i = 0; $i < 3; ++$i) {
                $ad = new Ad();
                $ad->setName('ad_name_'.$i);
                $ad->setContent('ad_content_'.$i);
                $ad->setTopic($topic);

                $this->entityManager->persist($ad);
            }

            $this->entityManager->flush();

            // when
            $result = $this->adRepository->countByTopic($topic);

            // then
            $this->assertEquals(3, $result);
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

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            $this->entityManager->persist($topic);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();

            // when
            $result = $this->adRepository
                ->queryAll()
                ->getQuery()
                ->getResult();

            // then
            $this->assertNotEmpty($result);
            $this->assertContainsOnlyInstancesOf(Ad::class, $result);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
