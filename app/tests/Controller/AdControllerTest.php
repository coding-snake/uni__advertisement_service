<?php
/**
 * Ad controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\AdService;
use App\Service\AdServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AdControllerTest.
 */
class AdControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entity_manager;
    private ?AdServiceInterface $ad_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->ad_service = $container->get(AdService::class);
    }

    /**
     * Test '/ads' route.
     */
    public function test_ad_page_render_default(): void
    {
        $this->client->request('GET', '/ads', ['page' => 1]);

        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/ads/topics/{id}/ads' route.
     */
    public function test_ads_per_topic_page_render(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entity_manager->persist($ad);

        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/topics/' . $topic->getId() . '/ads');

        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('ad_name', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/tags/{id}/ads' route.
     */
    public function test_ads_per_tag_page_render(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $tag = new Tag();
        $tag->setName('tag_name');
        $tag->setSlug('tag_slug');
        $tag->setAuthor($user);
        $this->entity_manager->persist($tag);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $ad->addTag($tag);

        $this->entity_manager->persist($ad);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/tags/' . $tag->getId() . '/ads');

        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('ad_name', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/ads/{id}' route.
     */
    public function test_ad_page_render_read(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $ad->setVerified(true);
        $this->entity_manager->persist($ad);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/' . $ad->getId());

        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('ad_content', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/ads/{id}/toggle-verification' route (Admin only).
     */
    public function test_ad_toggle_verification(): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('password_1');
        $admin->setUsername('admin_user');
        $admin->setRoles(['ROLE_ADMIN']);
        $this->entity_manager->persist($admin);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($admin);
        $ad->setTopic($topic);
        $ad->setVerified(false);
        $this->entity_manager->persist($ad);
        $this->entity_manager->flush();

        $this->client->loginUser($admin);
        $this->client->catchExceptions(false);

        $this->client->request('POST', '/ads/' . $ad->getId() . '/toggle-verification');

        $this->assertResponseRedirects('/ads/' . $ad->getId());

        $this->entity_manager->clear();
        $updated_ad = $this->entity_manager->getRepository(Ad::class)->find($ad->getId());

        $this->assertTrue($updated_ad->getVerified());
    }

    /**
     * Test create success — logged user.
     */
    public function test_ad_create_success(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $tag = new Tag();
        $tag->setName('tag_name');
        $tag->setSlug('tag_slug');
        $tag->setAuthor($user);
        $this->entity_manager->persist($tag);

        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/create');
        $this->client->submitForm('form-submit-button', [
            'ad[name]'    => 'ad_name',
            'ad[content]' => 'ad_content',
            'ad[topic]'   => (string) $topic->getId(),
            'ad[tags]'    => [(string) $tag->getId()],
        ]);

        $this->assertResponseRedirects('/ads');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->entity_manager->clear();
        $saved_ad = $this->entity_manager->getRepository(Ad::class)->findOneBy(['name' => 'ad_name']);

        $this->assertNotNull($saved_ad);
        $this->assertTrue($saved_ad->getVerified());
        $this->assertEquals('test_user', $saved_ad->getAuthor()->getUsername());
    }

    /**
     * Test ad create redirect when no topics exist.
     */
    public function test_ad_create_redirect_when_no_topics_exist(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);
        $this->entity_manager->flush();
        
        $this->client->loginUser($user);

        $topic_repository = $this->entity_manager->getRepository(Topic::class);
        $this->assertEquals(0, $topic_repository->count([]));
        
        $this->client->request('GET', '/ads/create');

        $router = static::getContainer()->get('router');
        $expected_path = $router->generate('topic_create');

        $this->assertResponseRedirects($expected_path);
    }
    
    /**
     * Test ad create sets verified to false for anonymous users.
     */
    public function test_ad_create_unverified_for_anonymous_user(): void
    {
        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);
        $this->entity_manager->flush();

        $this->client->request('GET', '/ads/create');

        $this->client->submitForm('form-submit-button', [
            'ad[name]'    => 'anonymous_ad',
            'ad[content]' => 'ad_content',
            'ad[topic]'   => (string) $topic->getId(),
        ]);

        $this->entity_manager->clear();
        $saved_ad = $this->entity_manager->getRepository(Ad::class)->findOneBy(['name' => 'anonymous_ad']);

        $this->assertNotNull($saved_ad);
        $this->assertFalse($saved_ad->getVerified());
        $this->assertNull($saved_ad->getAuthor());
    }

    /**
     * Test edit.
     */
    public function test_ad_edit_success(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $user->setRoles(['ROLE_ADMIN']);
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_1');
        $ad->setContent('content_1');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entity_manager->persist($ad);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/' . $ad->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('form-submit-button', [
            'ad[name]'    => 'ad_2',
            'ad[content]' => 'content_2',
            'ad[topic]'   => (string) $topic->getId(),
        ]);

        $router = static::getContainer()->get('router');
        $expected_path = $router->generate('ad_index');
        $this->assertResponseRedirects($expected_path);
        $this->client->followRedirect();

        $this->entity_manager->clear();
        $updated_ad = $this->entity_manager->getRepository(Ad::class)->find($ad->getId());

        $this->assertEquals('ad_2', $updated_ad->getName());
        $this->assertEquals('content_2', $updated_ad->getContent());
    }

    /**
     * Test delete.
     */
    public function test_ad_delete_success(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $user->setRoles(['ROLE_ADMIN']);
        $this->entity_manager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entity_manager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entity_manager->persist($ad);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $crawler = $this->client->request('GET', '/ads/' . $ad->getId() . '/delete');
        $form = $crawler->selectButton('form-submit-button')->form();
        $this->client->submit($form);

        $router = static::getContainer()->get('router');
        $expected_path = $router->generate('ad_index');
        $this->assertResponseRedirects($expected_path);
        $this->client->followRedirect();
    }
}