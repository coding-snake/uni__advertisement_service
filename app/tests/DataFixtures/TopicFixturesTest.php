<?php

/**
 * Topic fixtures tests.
 */

namespace App\Tests\DataFixtures;

use App\DataFixtures\TopicFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class TopicFixturesTest.
 */
class TopicFixturesTest extends TestCase
{
    /**
     * Test load method creates and persists a default topic.
     */
    public function testLoadDataCreatesTopic(): void
    {
        try {
            // given
            $mockManager = $this->createMock(ObjectManager::class);
            $mockReferenceRepository = $this->createMock(ReferenceRepository::class);

            $adminUser = new User();
            $adminUser->setEmail('admin@example.com');

            $mockReferenceRepository->expects($this->once())
                ->method('getReference')
                ->with('user-admin', User::class)
                ->willReturn($adminUser);

            $mockManager->expects($this->once())
                ->method('persist')
                ->with($this->callback(fn (Topic $topic) => 'default' === $topic->getName()
                && $adminUser === $topic->getAuthor()));

            $mockManager->expects($this->once())
                ->method('flush');

            $topicFixtures = new TopicFixtures();
            $topicFixtures->setReferenceRepository($mockReferenceRepository);

            // when
            $topicFixtures->load($mockManager);

            // then
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test getDependencies returns correct array.
     */
    public function testGetDependencies(): void
    {
        // given
        $topicFixtures = new TopicFixtures();

        // when
        $dependencies = $topicFixtures->getDependencies();

        // then
        $this->assertCount(1, $dependencies);
        $this->assertContains(UserFixtures::class, $dependencies);
    }
}
