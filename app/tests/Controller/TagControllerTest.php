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
    private ?EntityManagerInterface $entity_manager;
    private ?TagServiceInterface $tag_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->tag_service = $container->get(TagService::class);
    }

    /**
     * Test '/tags' route.
     */
    public function test_tag_page_render_default(): void
    {
        // when
        $this->client->request('GET', '/tags');

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/tags/{id}' route.
     */
    public function test_tag_page_render_read(): void
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

        $this->entity_manager->persist($user);
        $this->entity_manager->persist($tag);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/tags/' . $tag->getId());

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test '/tags/create' route.
     */
    public function test_tag_page_render_create(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_1');
        $user->setUsername('test');

        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

        $this->client->loginUser($user);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/tags/create');

        // then
        $this->assertResponseIsSuccessful();

        $response_content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<html', $response_content);
        $this->assertStringContainsString('</html>', $response_content);
    }

    /**
     * Test create.
     */
    public function test_tag_create(): void
    {
        try {
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
            $this->client->request('GET', '/tags/create');
            $this->client->submitForm('form-submit-button', [
                'tag[name]' => 'tag_name',
            ]);

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entity_manager->clear();

            $tag_repository = $this->entity_manager->getRepository(Tag::class);
            $saved_tag = $tag_repository->findOneBy(['name' => 'tag_name']);

            $this->assertNotNull($saved_tag);
            $this->assertEquals('tag_name', $saved_tag->getName());
            $this->assertEquals('test', $saved_tag->getAuthor()->getUsername());
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
    public function test_tag_edit(): void
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

            $this->entity_manager->persist($user);
            $this->entity_manager->persist($tag);
            $this->entity_manager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/tags/' . $tag->getId() . '/edit');
            $this->client->submitForm('form-submit-button', [
                'tag[name]' => 'tag_2',
            ]);

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entity_manager->clear();

            $tag_repository = $this->entity_manager->getRepository(Tag::class);
            $updated_tag = $tag_repository->find($tag->getId());

            $this->assertNotNull($updated_tag);
            $this->assertEquals('tag_2', $updated_tag->getName());
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
    public function test_tag_delete(): void
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

            $this->entity_manager->persist($user);
            $this->entity_manager->persist($tag);
            $this->entity_manager->flush();

            $this->client->loginUser($user);
            $this->client->catchExceptions(false);

            // when
            $this->client->request('GET', '/tags/' . $tag->getId() . '/delete');
            $this->client->submitForm('form-submit-button');

            // then
            $this->assertResponseRedirects('/tags');

            $this->client->followRedirect();
            $this->assertResponseIsSuccessful();

            $this->entity_manager->clear();

            $tag_repository = $this->entity_manager->getRepository(Tag::class);
            $deleted_tag = $tag_repository->find($tag->getId());

            $this->assertNull($deleted_tag);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}