<?php
/**
 * User repository tests.
 */

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Class UserRepositoryTest.
 */
class UserRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entity_manager;
    private ?UserRepository $user_repository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entity_manager = $container->get('doctrine.orm.entity_manager');
        $this->user_repository = $container->get(UserRepository::class);
    }

    /**
     * Test upgrade password success.
     */
    public function test_upgrade_password_success(): void
    {
        try {
            // given
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            $this->entity_manager->persist($user);
            $this->entity_manager->flush();

            // when
            $this->user_repository->upgradePassword($user, 'password_2');

            // then
            $this->entity_manager->clear();
            
            $updated_user = $this->user_repository->find($user->getId());

            $this->assertNotNull($updated_user);
            $this->assertSame('password_2', $updated_user->getPassword());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test upgrade password throws exception.
     */
    public function test_upgrade_password_throws_exception(): void
    {
            // given
            $invalid_user = new class implements PasswordAuthenticatedUserInterface {
                public function getPassword(): ?string
                {
                    return 'password_1';
                }
            };

            // when
            $this->expectException(UnsupportedUserException::class);
            $this->user_repository->upgradePassword($invalid_user, 'password_2');
    }
}