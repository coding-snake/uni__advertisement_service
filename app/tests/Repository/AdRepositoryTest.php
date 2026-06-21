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
    private ?EntityManagerInterface $entity_manager;
    private ?AdRepository $ad_repository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->ad_repository = $this->entity_manager->getRepository(Ad::class);
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

            $this->entity_manager->persist($topic);

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            // when
            $this->ad_repository->save($ad);

            // then
            $result = $this->ad_repository->find($ad->getId());

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
    public function test_delete(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            $this->entity_manager->persist($topic);
            $this->entity_manager->persist($ad);
            $this->entity_manager->flush();

            $id = $ad->getId();

            // when
            $this->ad_repository->delete($ad);

            // then
            $this->assertNull(
                $this->ad_repository->find($id)
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
    public function test_count_by_topic(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $this->entity_manager->persist($topic);

            for ($i = 0; $i < 3; ++$i) {
                $ad = new Ad();
                $ad->setName('ad_name_' . $i);
                $ad->setContent('ad_content_' . $i);
                $ad->setTopic($topic);

                $this->entity_manager->persist($ad);
            }

            $this->entity_manager->flush();

            // when
            $result = $this->ad_repository->countByTopic($topic);

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
    public function test_query_all(): void
    {
        try {
            // given
            $topic = new Topic();
            $topic->setName('topic_name');

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setTopic($topic);

            $this->entity_manager->persist($topic);
            $this->entity_manager->persist($ad);
            $this->entity_manager->flush();

            // when
            $result = $this->ad_repository
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