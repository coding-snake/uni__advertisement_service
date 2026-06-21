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
     * Test get and set.
     */
    public function testGetAndSet(): void
    {
        try {
            // given
            $topic = new Topic();
            $now = new \DateTimeImmutable();
            $user = new User();

            // when
            $topic->setName('topic_name');
            $topic->setCreatedAt($now);
            $topic->setUpdatedAt($now);
            $topic->setSlug('topic_slug');
            $topic->setAuthor($user);

            // then
            $this->assertEquals('topic_name', $topic->getName());
            $this->assertEquals($now, $topic->getCreatedAt());
            $this->assertEquals($now, $topic->getUpdatedAt());
            $this->assertEquals('topic_slug', $topic->getSlug());
            $this->assertSame($user, $topic->getAuthor());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
