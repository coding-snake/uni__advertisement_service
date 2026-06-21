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
    private ?EntityManagerInterface $entityManager;
    private ?AdServiceInterface $adService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->adService = $container->get(AdService::class);
    }

    /**
     * Test '/ads' route.
     */
    public function testAdPageRenderDefault(): void
    {
        $this->client->request('GET', '/ads', ['page' => 1]);

        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/ads/topics/{id}/ads' route.
     */
    public function testAdsPerTopicPageRender(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entityManager->persist($ad);

        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/topics/'.$topic->getId().'/ads');

        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('ad_name', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/tags/{id}/ads' route.
     */
    public function testAdsPerTagPageRender(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $tag = new Tag();
        $tag->setName('tag_name');
        $tag->setSlug('tag_slug');
        $tag->setAuthor($user);
        $this->entityManager->persist($tag);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $ad->addTag($tag);

        $this->entityManager->persist($ad);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/tags/'.$tag->getId().'/ads');

        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('ad_name', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/ads/{id}' route.
     */
    public function testAdPageRenderRead(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $ad = new Ad();
        $now = new \DateTimeImmutable();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt($now);
        $ad->setUpdatedAt($now);
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $ad->setVerified(true);
        $this->entityManager->persist($ad);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/'.$ad->getId());

        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('ad_content', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/ads/{id}/toggle-verification' route (Admin only).
     */
    public function testAdToggleVerification(): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('password_1');
        $admin->setUsername('admin_user');
        $admin->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($admin);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($admin);
        $ad->setTopic($topic);
        $ad->setVerified(false);
        $this->entityManager->persist($ad);
        $this->entityManager->flush();

        $this->client->loginUser($admin);
        $this->client->catchExceptions(false);

        $this->client->request('POST', '/ads/'.$ad->getId().'/toggle-verification');

        $this->assertResponseRedirects('/ads/'.$ad->getId());

        $this->entityManager->clear();
        $updatedAd = $this->entityManager->getRepository(Ad::class)->find($ad->getId());

        $this->assertTrue($updatedAd->getVerified());
    }

    /**
     * Test create success — logged user.
     */
    public function testAdCreateSuccess(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $tag = new Tag();
        $tag->setName('tag_name');
        $tag->setSlug('tag_slug');
        $tag->setAuthor($user);
        $this->entityManager->persist($tag);

        $this->entityManager->flush();

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

        $this->entityManager->clear();
        $savedAd = $this->entityManager->getRepository(Ad::class)->findOneBy(['name' => 'ad_name']);

        $this->assertNotNull($savedAd);
        $this->assertTrue($savedAd->getVerified());
        $this->assertEquals('test_user', $savedAd->getAuthor()->getUsername());
    }

    /**
     * Test ad create redirect when no topics exist.
     */
    public function testAdCreateRedirectWhenNoTopicsExist(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        $topicRepository = $this->entityManager->getRepository(Topic::class);
        $this->assertEquals(0, $topicRepository->count([]));

        $this->client->request('GET', '/ads/create');

        $router = static::getContainer()->get('router');
        $expectedPath = $router->generate('topic_create');

        $this->assertResponseRedirects($expectedPath);
    }

    /**
     * Test ad create sets verified to false for anonymous users.
     */
    public function testAdCreateUnverifiedForAnonymousUser(): void
    {
        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        $this->client->request('GET', '/ads/create');

        $this->client->submitForm('form-submit-button', [
            'ad[name]'    => 'anonymous_ad',
            'ad[content]' => 'ad_content',
            'ad[topic]'   => (string) $topic->getId(),
        ]);

        $this->entityManager->clear();
        $savedAd = $this->entityManager->getRepository(Ad::class)->findOneBy(['name' => 'anonymous_ad']);

        $this->assertNotNull($savedAd);
        $this->assertFalse($savedAd->getVerified());
        $this->assertNull($savedAd->getAuthor());
    }

    /**
     * Test edit.
     */
    public function testAdEditSuccess(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_1');
        $ad->setContent('content_1');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entityManager->persist($ad);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/ads/'.$ad->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('form-submit-button', [
            'ad[name]'    => 'ad_2',
            'ad[content]' => 'content_2',
            'ad[topic]'   => (string) $topic->getId(),
        ]);

        $router = static::getContainer()->get('router');
        $expectedPath = $router->generate('ad_index');
        $this->assertResponseRedirects($expectedPath);
        $this->client->followRedirect();

        $this->entityManager->clear();
        $updatedAd = $this->entityManager->getRepository(Ad::class)->find($ad->getId());

        $this->assertEquals('ad_2', $updatedAd->getName());
        $this->assertEquals('content_2', $updatedAd->getContent());
    }

    /**
     * Test delete.
     */
    public function testAdDeleteSuccess(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test_user');
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);

        $topic = new Topic();
        $topic->setName('topic_name');
        $this->entityManager->persist($topic);

        $ad = new Ad();
        $ad->setName('ad_name');
        $ad->setContent('ad_content');
        $ad->setCreatedAt(new \DateTimeImmutable());
        $ad->setUpdatedAt(new \DateTimeImmutable());
        $ad->setAuthor($user);
        $ad->setTopic($topic);
        $this->entityManager->persist($ad);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);

        $crawler = $this->client->request('GET', '/ads/'.$ad->getId().'/delete');
        $form = $crawler->selectButton('form-submit-button')->form();
        $this->client->submit($form);

        $router = static::getContainer()->get('router');
        $expectedPath = $router->generate('ad_index');
        $this->assertResponseRedirects($expectedPath);
        $this->client->followRedirect();
    }
}
