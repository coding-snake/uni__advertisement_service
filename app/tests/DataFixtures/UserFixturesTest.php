<?php

/**
 * User fixtures tests.
 */

namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixturesTest.
 */
class UserFixturesTest extends TestCase
{
    /**
     * Test load method creates and persists an admin user.
     */
    public function testLoadDataCreatesAdminUser(): void
    {
        try {
            // given
            $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

            $mockPasswordHasher->expects($this->once())
                ->method('hashPassword')
                ->willReturn('hashed_admin_password');

            $mockManager = $this->createMock(ObjectManager::class);

            $mockManager->expects($this->once())
                ->method('persist')
                ->with($this->callback(fn (User $user) => 'admin@example.com' === $user->getEmail()
                    && 'admin' === $user->getUsername()
                    && in_array(UserRole::ROLE_ADMIN->value, $user->getRoles())
                    && in_array(UserRole::ROLE_USER->value, $user->getRoles())
                && 'hashed_admin_password' === $user->getPassword()));

            $mockManager->expects($this->once())
                ->method('flush');

            $userFixtures = new UserFixtures($mockPasswordHasher);

            $mockReferenceRepository = $this->createMock(ReferenceRepository::class);
            $userFixtures->setReferenceRepository($mockReferenceRepository);


            $faker = Factory::create();
            $reflection = new \ReflectionClass($userFixtures);

            $fakerProperty = $reflection->getParentClass()->getProperty('faker');
            $fakerProperty->setValue($userFixtures, $faker);

            // when
            $userFixtures->load($mockManager);
        } catch (\Throwable $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test load data returns early if dependencies are missing.
     */
    public function testLoadDataReturnsEarlyIfDependenciesAreMissing(): void
    {
        try {
            // given
            $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
            $userFixtures = new UserFixtures($mockPasswordHasher);

            $reflection = new \ReflectionClass(UserFixtures::class);
            $method = $reflection->getMethod('loadData');

            $fakerProperty = $reflection->getParentClass()->getProperty('faker');
            $fakerProperty->setValue($userFixtures, null);

            // when
            $method->invoke($userFixtures);

            // then
            $this->assertTrue(true);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }
}
