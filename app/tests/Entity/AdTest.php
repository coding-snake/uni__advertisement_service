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
     * Test get and set.
     */
    public function test_get_and_set(): void
    {
        try {
            // given
            $ad = new Ad();
            $now = new \DateTimeImmutable();
            $topic = new Topic();
            $user = new User();

            // when
            $ad->setName('ad_name');
            $ad->setContent('ad_content');
            $ad->setCreatedAt($now);
            $ad->setUpdatedAt($now);
            $ad->setTopic($topic);
            $ad->setVerified(true);
            $ad->setSlug('ad_slug');
            $ad->setAuthor($user);

            // then
            $this->assertEquals('ad_name', $ad->getName());
            $this->assertEquals('ad_content', $ad->getContent());
            $this->assertEquals($now, $ad->getCreatedAt());
            $this->assertEquals($now, $ad->getUpdatedAt());
            $this->assertSame($topic, $ad->getTopic());
            $this->assertTrue($ad->getVerified());
            $this->assertEquals('ad_slug', $ad->getSlug());
            $this->assertSame($user, $ad->getAuthor());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test tag interactions.
     */
    public function test_tag_interactions(): void
    {
        try {
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
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}