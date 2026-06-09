<?php

/**
 * Ad entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class AdTest.
 */
class AdTest extends TestCase
{
    /**
     * Instead of a bunch of small functions, just do one massive Ad entity
     */
    public function testGetAndSet(): void
    {
        // given
        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $topic = new Topic();
        $user = new User();

        // when
        $ad->setName('Test');
        $ad->setContent('Test content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setTopic($topic);
        $ad->setVerified(true);
        $ad->setSlug('test');
        $ad->setAuthor($user);

        // then
        $this->assertEquals('Test', $ad->getName());
        $this->assertEquals('Test content', $ad->getContent());
        $this->assertEquals($now, $ad->getCreatedAt());
        $this->assertEquals($now, $ad->getUpdatedAt());
        $this->assertSame($topic, $ad->getTopic());
        $this->assertTrue($ad->getVerified());
        $this->assertEquals('test', $ad->getSlug());
        $this->assertSame($user, $ad->getAuthor());
        $this->assertSame(1, $ad->getId());
    }

    /**
     * Test interactions with tag
     */
    public function testTagInteractions(): void
    {
        // given
        $ad = new Ad();
        $tag = new Tag();

        // when
        $ad->addTag($tag);

        // then
        $this->assertCount(1, $ad->getTags());
        $this->assertTrue($ad->getTags()->contains($tag));

        // when
        $ad->removeTag($tag);

        // then
        $this->assertCount(0, $ad->getTags());
        $this->assertFalse($ad->getTags()->contains($tag));
    }
}