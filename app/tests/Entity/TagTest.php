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
     * Instead of a bunch of small functions, just do one massive Tag entity
     */
    public function testGetAndSet(): void
    {
        // given
        $tag = new Tag();
        $now = new \DateTimeImmutable();
        $user = new User();

        // when
        $tag->setName('Tag');
        $tag->setCreatedAt($now);
        $tag->setUpdatedAt($now);
        $tag->setSlug('tag');
        $tag->setAuthor($user);

        // then
        $this->assertEquals('Tag', $tag->getName());
        $this->assertEquals($now, $tag->getCreatedAt());
        $this->assertEquals($now, $tag->getUpdatedAt());
        $this->assertEquals('tag', $tag->getSlug());
        $this->assertSame($user, $tag->getAuthor());
        $this->assertSame(1, $tag->getId());
    }

    /**
     * Test interactions with ad
     */
    public function testAdCollectionManagement(): void
    {
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
    }
}