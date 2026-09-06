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
    private ?EntityManagerInterface $entityManager;
    private ?UserRepository $userRepository;

    /**
     * Set up tests.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->userRepository = $container->get(UserRepository::class);
    }

    /**
     * Test upgrade password success.
     */
    public function testUpgradePasswordSuccess(): void
    {
        try {
            // given
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // when
            $this->userRepository->upgradePassword($user, 'password_2');

            // then
            $this->entityManager->clear();

            $updatedUser = $this->userRepository->find($user->getId());

            $this->assertNotNull($updatedUser);
            $this->assertSame('password_2', $updatedUser->getPassword());
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
    public function testUpgradePasswordThrowsException(): void
    {
        // Używamy natywnego mechanizmu PHPUnit do stworzenia "fałszywego" użytkownika.
        // Rozwiązuje to problem z formatowaniem klas anonimowych przez PHP CS Fixer.
        $invalidUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->userRepository->upgradePassword($invalidUser, 'password_2');
    }
}
