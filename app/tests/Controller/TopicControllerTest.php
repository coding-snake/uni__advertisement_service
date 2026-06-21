<?php

/**
 * Topic controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Ad;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\TopicService;
use App\Service\TopicServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TopicControllerTest.
 */
class TopicControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;
    private ?TopicServiceInterface $topicService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->topicService = $container->get(TopicService::class);
    }

    /**
     * Test '/topics' route.
     */
    public function testTopicPageRenderDefault(): void
    {
        // when
        $this->client->request('GET', '/topics');

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/topics/{id}' route.
     */
    public function testTopicPageRenderRead(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $topic = new Topic();
        $now = new \DateTimeImmutable();
        $topic->setName('topic_name');
        $topic->setCreatedAt($now);
        $topic->setUpdatedAt($now);
        $topic->setSlug('topic_slug');
        $topic->setAuthor($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/topics/'.$topic->getId());

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/topics/create' route.
     */
    public function testTopicPageRenderCreate(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/topics/create');

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test create.
     */
    public function testTopicCreate(): void
    {
        // given
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        // when
        $this->client->request('GET', '/topics/create');
        $this->client->submitForm('form-submit-button', [
            'topic[name]' => 'topic_name',
        ]);

        // then
        $this->assertResponseRedirects('/topics');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();

        $topicRepository = $this->entityManager->getRepository(Topic::class);
        $savedTopic = $topicRepository->findOneBy(['name' => 'topic_name']);

        $this->assertNotNull($savedTopic);
        $this->assertEquals('topic_name', $savedTopic->getName());
        $this->assertEquals('test', $savedTopic->getAuthor()->getUsername());
    }

    /**
     * Test edit.
     */
    public function testTopicEdit(): void
    {
        // given
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $topic = new Topic();
        $now = new \DateTimeImmutable();
        $topic->setName('topic_1');
        $topic->setCreatedAt($now);
        $topic->setUpdatedAt($now);
        $topic->setSlug('topic_1');
        $topic->setAuthor($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        // when
        $this->client->request('GET', '/topics/'.$topic->getId().'/edit');
        $this->client->submitForm('form-submit-button', [
            'topic[name]' => 'topic_2',
        ]);

        // then
        $this->assertResponseRedirects('/topics');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();

        $topicRepository = $this->entityManager->getRepository(Topic::class);
        $updatedTopic = $topicRepository->find($topic->getId());

        $this->assertNotNull($updatedTopic);
        $this->assertEquals('topic_2', $updatedTopic->getName());
    }

    /**
     * Test delete.
     */
    public function testTopicDeleteSuccess(): void
    {
        // given
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $topic = new Topic();
        $now = new \DateTimeImmutable();
        $topic->setName('topic_name');
        $topic->setCreatedAt($now);
        $topic->setUpdatedAt($now);
        $topic->setSlug('topic_slug');
        $topic->setAuthor($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        // when
        $this->client->request('GET', '/topics/'.$topic->getId().'/delete');
        $this->client->submitForm('form-submit-button');

        // then
        $this->assertResponseRedirects('/topics');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();

        $topicRepository = $this->entityManager->getRepository(Topic::class);
        $deletedTopic = $topicRepository->find($topic->getId());

        $this->assertNull($deletedTopic);
    }

    /**
     * Test delete fails when topic has ads.
     */
    public function testTopicDeleteFailHasAds(): void
    {
        try {
            // given
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            $topic = new Topic();
            $now = new \DateTimeImmutable();
            $topic->setName('topic_name');
            $topic->setCreatedAt($now);
            $topic->setUpdatedAt($now);
            $topic->setSlug('topic_slug');
            $topic->setAuthor($user);

            $ad = new Ad();
            $ad->setName('ad_name');
            $ad->setTopic($topic);
            $ad->setAuthor($user);
            $ad->setContent('ad_content');

            $this->entityManager->persist($user);
            $this->entityManager->persist($topic);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/topics/'.$topic->getId().'/delete');

            // then
            $this->assertResponseRedirects('/topics');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->assertSelectorExists('.alert-warning');

            $this->entityManager->clear();
            $topicRepository = $this->entityManager->getRepository(Topic::class);
            $savedTopic = $topicRepository->find($topic->getId());

            $this->assertNotNull($savedTopic);
            $this->assertEquals('topic_name', $savedTopic->getName());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
