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
    private ?EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Test account index page.
     */
    public function test_account_index(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test_user');
            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

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
    public function test_change_username(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('original_user');
            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

            $this->client->loginUser($user);

            // when
            $this->client->request('GET', '/account/change_username');
            $this->client->submitForm('form-submit-button', [
                'username[username]' => 'new_name',
            ]);

            // then
            $this->assertResponseRedirects('/account/');

            $this->entity_manager->clear();
            $updated_user = $this->entity_manager->getRepository(User::class)->find($user->getId());
            $this->assertEquals('new_name', $updated_user->getUsername());
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
    public function test_change_password(): void
    {
        try {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('old_password');
            $user->setUsername('test_user');
            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

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