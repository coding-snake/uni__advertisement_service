<?php

/**
 * Ad fixtures.
 */

namespace App\DataFixtures;

use App\DataFixtures\TopicFixtures;
use App\DataFixtures\TagFixtures;
use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class AdFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class AdFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
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

        $this->createMany(100, 'ad', function (int $i) {
            $ad = new Ad();
            $ad->setName($this->faker->sentence);
            $ad->setContent($this->faker->text);
            $ad->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $ad->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $topic = $this->getRandomReference('topic', Topic::class);
            $ad->setTopic($topic);

            $tags = $this->getRandomReferenceList('tag', Tag::class, rand(1, 3));

            foreach ($tags as $tag) {
                $ad->addTag($tag);
            }

            /** @var User $author */
            $author = $this->getRandomReference('user', User::class);
            $ad->setAuthor($author);

            return $ad;
        });
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: TopicFixtures::class}
     */
    public function getDependencies(): array
    {
        return [
            TopicFixtures::class,
            TagFixtures::class,
            UserFixtures::class];
    }
}
