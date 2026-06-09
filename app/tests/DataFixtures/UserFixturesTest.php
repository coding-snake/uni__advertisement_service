<?php
/**
 * User Fixtures test.
 */

namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Enum\UserRole;
use App\Entity\User;
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
        // given
        
        // Setting up the ObjectManager and password hasher that I need to work
        $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $mockPasswordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_admin_password');

        $mockManager = $this->createMock(ObjectManager::class);

        // Settings for admin data
        $mockManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (User $user) {
                return $user->getEmail() === 'admin@example.com' 
                    && $user->getUsername() === 'admin'
                    && in_array(UserRole::ROLE_ADMIN->value, $user->getRoles())
                    && in_array(UserRole::ROLE_USER->value, $user->getRoles())
                    && $user->getPassword() === 'hashed_admin_password';
            }));

        $mockManager->expects($this->once())
            ->method('flush');

        $userFixtures = new UserFixtures($mockPasswordHasher);

        $faker = Factory::create();
        $reflection = new \ReflectionClass($userFixtures);
        
        $fakerProperty = $reflection->getParentClass()->getProperty('faker');
        $fakerProperty->setValue($userFixtures, $faker);

        // when
        $userFixtures->load($mockManager);

        // then
    }
}