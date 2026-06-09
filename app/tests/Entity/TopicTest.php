<?php

/**
 * Topic entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Topic;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class TopicTest.
 */
class TopicTest extends TestCase
{
    /**
     * Instead of a bunch of small functions, just do one massive Topic entity
     */
    public function testGetAndSet(): void
    {
        // given
        $topic = new Topic();
        $now = new \DateTimeImmutable();
        $user = new User();

        // when
        $topic->setName('Topic');
        $topic->setCreatedAt($now);
        $topic->setUpdatedAt($now);
        $topic->setSlug('topic');
        $topic->setAuthor($user);

        // then
        $this->assertEquals('Topic', $topic->getName());
        $this->assertEquals($now, $topic->getCreatedAt());
        $this->assertEquals($now, $topic->getUpdatedAt());
        $this->assertEquals('topic', $topic->getSlug());
        $this->assertSame($user, $topic->getAuthor());
        $this->assertSame(1, $topic->getId());
    }
}