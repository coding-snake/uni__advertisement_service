<?php

/**
 * Tag controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TagControllerTest.
 */
class TagControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;
    private ?TagServiceInterface $tagService;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagService = $container->get(TagService::class);
    }

    /**
     * Test '/tags' route.
     */
    public function testTagPageRenderDefault(): void
    {
        // when
        $this->client->request('GET', '/tags');

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/tags/{id}' route.
     */
    public function testTagPageRenderRead(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $tag = new Tag();
        $now = new \DateTimeImmutable();
        $tag->setName('tag_name');
        $tag->setCreatedAt($now);
        $tag->setUpdatedAt($now);
        $tag->setSlug('tag_slug');
        $tag->setAuthor($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/tags/'.$tag->getId());

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test '/tags/create' route.
     */
    public function testTagPageRenderCreate(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/tags/create');

        // then
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $responseContent);
        $this->assertStringContainsString('</html>', $responseContent);
    }

    /**
     * Test create.
     */
    public function testTagCreate(): void
    {
        try {
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
            $this->client->request('GET', '/tags/create');
            $this->client->submitForm('form-submit-button', [
                'tag[name]' => 'tag_name',
            ]);

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entityManager->clear();

            $tagRepository = $this->entityManager->getRepository(Tag::class);
            $savedTag = $tagRepository->findOneBy(['name' => 'tag_name']);

            $this->assertNotNull($savedTag);
            $this->assertEquals('tag_name', $savedTag->getName());
            $this->assertEquals('test', $savedTag->getAuthor()->getUsername());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test edit.
     */
    public function testTagEdit(): void
    {
        try {
            // given
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            $tag = new Tag();
            $now = new \DateTimeImmutable();
            $tag->setName('tag_1');
            $tag->setCreatedAt($now);
            $tag->setUpdatedAt($now);
            $tag->setSlug('tag_1');
            $tag->setAuthor($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/tags/'.$tag->getId().'/edit');
            $this->client->submitForm('form-submit-button', [
                'tag[name]' => 'tag_2',
            ]);

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entityManager->clear();

            $tagRepository = $this->entityManager->getRepository(Tag::class);
            $updatedTag = $tagRepository->find($tag->getId());

            $this->assertNotNull($updatedTag);
            $this->assertEquals('tag_2', $updatedTag->getName());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test delete.
     */
    public function testTagDelete(): void
    {
        try {
            // given
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            $tag = new Tag();
            $now = new \DateTimeImmutable();
            $tag->setName('tag_d');
            $tag->setCreatedAt($now);
            $tag->setUpdatedAt($now);
            $tag->setSlug('tag_slug');
            $tag->setAuthor($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/tags/'.$tag->getId().'/delete');
            $this->client->submitForm('form-submit-button');

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entityManager->clear();

            $tagRepository = $this->entityManager->getRepository(Tag::class);
            $deletedTag = $tagRepository->find($tag->getId());

            $this->assertNull($deletedTag);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
