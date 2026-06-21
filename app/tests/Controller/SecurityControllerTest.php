<?php
/**
 * Security controller tests.
 */

namespace App\Tests\Controller;

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
    private ?EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Test register success.
     */
    public function test_register_success(): void
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
            $expected_path = $router->generate('app_login');

            $this->assertResponseRedirects($expected_path);

            $user_repository = $this->entity_manager->getRepository(User::class);
            $user = $user_repository->findOneBy(['email' => 'test@example.com']);
            $this->assertNotNull($user);
        } catch (\Exception $e) {
            dd(['Error' => $e->getMessage(), 'File' => $e->getFile(), 'Line' => $e->getLine()]);
        }
    }

    /**
     * Test register redirect to home.
     */
    public function test_register_redirect_to_home(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test_user');
        $user->setPassword('password_123');
        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

        $this->client->loginUser($user);

        $this->client->request('GET', '/security/register');

        $router = static::getContainer()->get('router');
        $expected_path = $router->generate('home');

        $this->assertResponseRedirects($expected_path);
    }

    /**
     * Test register fail duplicate email.
     */
    public function test_register_fail_duplicate_email(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password_123');
        $user->setUsername('test_user');
        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

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
    public function test_login_redirect(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_123');
            $user->setUsername('test_user');
            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

            $this->client->loginUser($user);

            $this->client->request('GET', '/security/login');

            $router = static::getContainer()->get('router');
            $expected_path = $router->generate('home');

            $this->assertResponseRedirects($expected_path);
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
    public function test_login_success(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setUsername('test_user');

            $container = static::getContainer();
            $hasher = $container->get('security.password_hasher');
            $user->setPassword($hasher->hashPassword($user, 'password_123'));

            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

            $crawler = $this->client->request('GET', '/security/login');
            $form = $crawler->selectButton('form-submit-button')->form([
                '_username' => 'test@example.com',
                '_password' => 'password_123',
            ]);
            $this->client->submit($form);

            $router = static::getContainer()->get('router');
            $expected_path = $router->generate('home');
            $this->assertResponseRedirects($expected_path);
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
    public function test_logout_exception(): void
    {
        $controller = new \App\Controller\SecurityController();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank');

        $controller->logout();
    }
}