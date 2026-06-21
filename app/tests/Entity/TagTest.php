<?php
/**
 * Tag entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class TagTest.
 */
class TagTest extends TestCase
{
    /**
     * Test get and set.
     */
    public function test_get_and_set(): void
    {
        try {
            // given
            $tag = new Tag();
            $now = new \DateTimeImmutable();
            $user = new User();

            // when
            $tag->setName('tag_name');
            $tag->setCreatedAt($now);
            $tag->setUpdatedAt($now);
            $tag->setSlug('tag_slug');
            $tag->setAuthor($user);

            // then
            $this->assertEquals('tag_name', $tag->getName());
            $this->assertEquals($now, $tag->getCreatedAt());
            $this->assertEquals($now, $tag->getUpdatedAt());
            $this->assertEquals('tag_slug', $tag->getSlug());
            $this->assertSame($user, $tag->getAuthor());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test ad collection management.
     */
    public function test_ad_collection_management(): void
    {
        try {
            // given
            $tag = new Tag();
            $ad = new Ad();

            // when
            $tag->addAd($ad);

            // then
            $this->assertCount(1, $tag->getAds());
            $this->assertTrue($tag->getAds()->contains($ad));

            // when
            $tag->removeAd($ad);

            // then
            $this->assertCount(0, $tag->getAds());
            $this->assertFalse($tag->getAds()->contains($ad));
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}