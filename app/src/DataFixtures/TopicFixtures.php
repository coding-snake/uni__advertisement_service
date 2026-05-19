<?php

/**
 * Topic fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class Topic.
 *
 * @psalm-suppress MissingConstructor
 */
class TopicFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(20, 'topic', function (int $i) {
            $topic = new Topic();
            $topic->setName($this->faker->unique()->word);
            $topic->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $topic->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            return $topic;
        });
    }
}
