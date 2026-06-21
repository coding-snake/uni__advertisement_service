<?php

/**
 * Account controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AccountControllerTest.
 */
class AccountControllerTest extends WebTestCase
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
     * Test account index page.
     */
    public function testAccountIndex(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test_user');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->client->loginUser($user);
            $this->client->request('GET', '/account/');

            $this->assertResponseIsSuccessful();
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test change username.
     */
    public function testChangeUsername(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('original_user');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->client->loginUser($user);

            // when
            $this->client->request('GET', '/account/change_username');
            $this->client->submitForm('form-submit-button', [
                'username[username]' => 'new_name',
            ]);

            // then
            $this->assertResponseRedirects('/account/');

            $this->entityManager->clear();
            $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
            $this->assertEquals('new_name', $updatedUser->getUsername());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test change password.
     */
    public function testChangePassword(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('old_password');
            $user->setUsername('test_user');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->assertInstanceOf(User::class, $user);

            $this->client->loginUser($user);

            // when
            $this->client->request('GET', '/account/change_password');
            $this->client->submitForm('form-submit-button', [
                'change_password[plainPassword][first]'  => 'new_password',
                'change_password[plainPassword][second]' => 'new_password',
            ]);

            // then
            $this->assertResponseRedirects('/account/');
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
