<?php

/**
 * Topic Fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TopicFixtures.
 */
class TopicFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Get dependencies.
     *
     * @return list<class-string> Dependencies
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        /** @var User $admin */
        $admin = $this->getReference('user-admin', User::class);

        $topic = new Topic();
        $topic->setName('default');
        $topic->setAuthor($admin);

        $this->manager->persist($topic);
        $this->manager->flush();
    }
}
