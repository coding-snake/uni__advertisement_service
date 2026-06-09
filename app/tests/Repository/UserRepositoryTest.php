<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?UserRepository $userRepository = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);
        $this->userRepository = $repository;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager?->close();
        $this->entityManager = null;
        $this->userRepository = null;
    }

    /**
     * Testuje podstawowe zapisywanie i wyszukiwanie użytkownika (pokrycie find()).
     */
    public function testSaveAndFindUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('test');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $result = $this->userRepository->find($user->getId());

        $this->assertNotNull($result);
        $this->assertEquals('test@example.com', $result->getEmail());
    }

    /**
     * Testuje usuwanie użytkownika.
     */
    public function testDeleteUser(): void
    {
        $user = new User();
        $user->setEmail('delete-test@example.com');
        $user->setPassword('some-password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $id = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->assertNull($this->userRepository->find($id));
    }

    /**
     * Testuje poprawną zmianę hasła.
     */
    public function testUpgradePassword(): void
    {
        // given
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('old-password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        // when
        $this->userRepository->upgradePassword($user, 'new-password');

        // then
        $this->entityManager->clear();

        $updatedUser = $this->userRepository->find($userId);

        $this->assertNotNull($updatedUser);
        $this->assertSame('new-password', $updatedUser->getPassword());
    }

    public function testUpgradePasswordThrowsExceptionForInvalidUser(): void
    {
        $invalidUser = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string
            {
                return 'password';
            }
        };

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', $invalidUser::class));

        $this->userRepository->upgradePassword($invalidUser, 'new-password');
    }
}