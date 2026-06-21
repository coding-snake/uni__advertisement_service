<?php

/**
 * Security controller tests.
 */

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest.
 */
class SecurityControllerTest extends WebTestCase
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
     * Test register success.
     */
    public function testRegisterSuccess(): void
    {
        try {
            $crawler = $this->client->request('GET', '/security/register');
            $form = $crawler->selectButton('form-submit-button')->form([
                'user[email]' => 'test@example.com',
                'user[username]' => 'test_user',
                'user[password][first]' => 'password_123',
                'user[password][second]' => 'password_123',
            ]);
            $this->client->submit($form);

            $router = static::getContainer()->get('router');
            $expectedPath = $router->generate('app_login');

            $this->assertResponseRedirects($expectedPath);

            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => 'test@example.com']);
            $this->assertNotNull($user);
        } catch (\Exception $e) {
            dd(['Error' => $e->getMessage(), 'File' => $e->getFile(), 'Line' => $e->getLine()]);
        }
    }

    /**
     * Test register redirect to home.
     */
    public function testRegisterRedirectToHome(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test_user');
        $user->setPassword('password_123');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        $this->client->request('GET', '/security/register');

        $router = static::getContainer()->get('router');
        $expectedPath = $router->generate('home');

        $this->assertResponseRedirects($expectedPath);
    }

    /**
     * Test register fail duplicate email.
     */
    public function testRegisterFailDuplicateEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_123');
        $user->setUsername('test_user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/security/register');

        $form = $crawler->selectButton('form-submit-button')->form([
            'user[email]' => 'test@example.com',
            'user[username]' => 'other_user',
            'user[password][first]' => 'password_123',
            'user[password][second]' => 'password_123',
        ]);
        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    /**
     * Test login redirect.
     */
    public function testLoginRedirect(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_123');
            $user->setUsername('test_user');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->client->loginUser($user);

            $this->client->request('GET', '/security/login');

            $router = static::getContainer()->get('router');
            $expectedPath = $router->generate('home');

            $this->assertResponseRedirects($expectedPath);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test login success.
     */
    public function testLoginSuccess(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setUsername('test_user');

            $container = static::getContainer();
            $hasher = $container->get('security.password_hasher');
            $user->setPassword($hasher->hashPassword($user, 'password_123'));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $crawler = $this->client->request('GET', '/security/login');
            $form = $crawler->selectButton('form-submit-button')->form([
                '_username' => 'test@example.com',
                '_password' => 'password_123',
            ]);
            $this->client->submit($form);

            $router = static::getContainer()->get('router');
            $expectedPath = $router->generate('home');
            $this->assertResponseRedirects($expectedPath);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * The logout exception test.
     */
    public function testLogoutException(): void
    {
        $controller = new SecurityController();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank');

        $controller->logout();
    }
}
