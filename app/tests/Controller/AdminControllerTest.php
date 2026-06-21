<?php

/**
 * Admin controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AdminControllerTest.
 */
class AdminControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Test admin index.
     */
    public function testAdminIndex(): void
    {
        try {
            $admin = new User();
            $admin->setEmail('admin@example.com');
            $admin->setPassword('password_1');
            $admin->setUsername('admin_user');
            $admin->setRoles(['ROLE_ADMIN']);
            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            $this->client->loginUser($admin);
            $this->client->request('GET', '/admin/', ['page' => 1]);

            $this->assertResponseIsSuccessful();
            $responseContent = $this->client->getResponse()->getContent();

            $this->assertStringContainsString('<html', $responseContent);
            $this->assertStringContainsString('</html>', $responseContent);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test admin user index.
     */
    public function testAdminUserIndex(): void
    {
        try {
            $admin = new User();
            $admin->setEmail('admin@example.com');
            $admin->setPassword('password_1');
            $admin->setUsername('admin_user');
            $admin->setRoles(['ROLE_ADMIN']);

            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            $this->client->loginUser($admin);

            // Testing default page (1)
            $this->client->request('GET', '/admin/users');
            $this->assertResponseIsSuccessful();

            // Testing specific page parameter
            $this->client->request('GET', '/admin/users', ['page' => 2]);
            $this->assertResponseIsSuccessful();

            $this->assertSelectorExists('table'); // Ensure the table is rendered
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test edit user username.
     */
    public function testAdminEditUsername(): void
    {
        try {
            $admin = new User();
            $admin->setEmail('admin@example.com');
            $admin->setPassword('password_1');
            $admin->setUsername('admin_user');
            $admin->setRoles(['ROLE_ADMIN']);

            $user = new User();
            $user->setEmail('target@example.com');
            $user->setPassword('password_1');
            $user->setUsername('target_user');

            $this->entityManager->persist($admin);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->client->loginUser($admin);

            // when
            $this->client->request('GET', '/admin/users/'.$user->getId().'/edit-username');
            $this->client->submitForm('form-submit-button', [
                'username[username]' => 'updated_name',
            ]);

            // then
            $this->assertResponseRedirects('/admin/users');

            $this->entityManager->clear();
            $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
            $this->assertEquals('updated_name', $updatedUser->getUsername());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test edit user password.
     */
    public function testAdminEditPassword(): void
    {
        try {
            $admin = new User();
            $admin->setEmail('admin@example.com');
            $admin->setPassword('password_1');
            $admin->setUsername('admin_user');
            $admin->setRoles(['ROLE_ADMIN']);

            $user = new User();
            $user->setEmail('target@example.com');
            $user->setPassword('old_password');
            $user->setUsername('target_user');

            $this->entityManager->persist($admin);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->client->loginUser($admin);

            // when
            $this->client->request('GET', '/admin/users/'.$user->getId().'/edit-password');
            $this->client->submitForm('form-submit-button', [
                'change_password[plainPassword][first]' => 'new_password',
                'change_password[plainPassword][second]' => 'new_password',
            ]);

            // then
            $this->assertResponseRedirects('/admin/users');
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
