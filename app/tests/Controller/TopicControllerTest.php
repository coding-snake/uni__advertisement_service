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
    private ?EntityManagerInterface $entity_manager;
    private ?TopicServiceInterface $topic_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->topic_service = $container->get(TopicService::class);
    }

    /**
     * Test '/topics' route.
     */
    public function test_topic_page_render_default(): void
    {
        // when
        $this->client->request('GET', '/topics');

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/topics/{id}' route.
     */
    public function test_topic_page_render_read(): void
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

        $this->entity_manager->persist($user);
        $this->entity_manager->persist($topic);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/topics/' . $topic->getId());

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/topics/create' route.
     */
    public function test_topic_page_render_create(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/topics/create');

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test create.
     */
    public function test_topic_create(): void
    {
        // given
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

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

        $this->entity_manager->clear();

        $topic_repository = $this->entity_manager->getRepository(Topic::class);
        $saved_topic = $topic_repository->findOneBy(['name' => 'topic_name']);

        $this->assertNotNull($saved_topic);
        $this->assertEquals('topic_name', $saved_topic->getName());
        $this->assertEquals('test', $saved_topic->getAuthor()->getUsername());
    }

    /**
     * Test edit.
     */
    public function test_topic_edit(): void
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

        $this->entity_manager->persist($user);
        $this->entity_manager->persist($topic);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        // when
        $this->client->request('GET', '/topics/' . $topic->getId() . '/edit');
        $this->client->submitForm('form-submit-button', [
            'topic[name]' => 'topic_2',
        ]);

        // then
        $this->assertResponseRedirects('/topics');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entity_manager->clear();

        $topic_repository = $this->entity_manager->getRepository(Topic::class);
        $updated_topic = $topic_repository->find($topic->getId());

        $this->assertNotNull($updated_topic);
        $this->assertEquals('topic_2', $updated_topic->getName());
    }

    /**
     * Test delete.
     */
    public function test_topic_delete_success(): void
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

        $this->entity_manager->persist($user);
        $this->entity_manager->persist($topic);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        // when
        $this->client->request('GET', '/topics/' . $topic->getId() . '/delete');
        $this->client->submitForm('form-submit-button');

        // then
        $this->assertResponseRedirects('/topics');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entity_manager->clear();

        $topic_repository = $this->entity_manager->getRepository(Topic::class);
        $deleted_topic = $topic_repository->find($topic->getId());

        $this->assertNull($deleted_topic);
    }

    /**
     * Test delete fails when topic has ads.
     */
    public function test_topic_delete_fail_has_ads(): void
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

            $this->entity_manager->persist($user);
            $this->entity_manager->persist($topic);
            $this->entity_manager->persist($ad);
            $this->entity_manager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/topics/' . $topic->getId() . '/delete');

            // then
            $this->assertResponseRedirects('/topics');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->assertSelectorExists('.alert-warning');

            $this->entity_manager->clear();
            $topic_repository = $this->entity_manager->getRepository(Topic::class);
            $saved_topic = $topic_repository->find($topic->getId());

            $this->assertNotNull($saved_topic);
            $this->assertEquals('topic_name', $saved_topic->getName());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}