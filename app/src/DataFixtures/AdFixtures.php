<?php
/**
 * Task fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Ad;

/**
 * Class TaskFixtures.
 */

    /**
     * Load.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    class AdFixtures extends AbstracyBaseFixtures
    {
        /**
         * Load data.
         */

        public function loadData(): void
        {
        for ($i = 0; $i < 10; ++$i) {
            $ad = new Ad();
            $ad->setName($this->faker->sentence);
            $ad->setContent($this->faker->sentence);
            $ad->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $ad->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
