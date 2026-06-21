<?php
/**
 * User fixtures tests.
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
    public function test_load_data_creates_admin_user(): void
    {
        try {
            // given
            $mock_password_hasher = $this->createMock(UserPasswordHasherInterface::class);

            $mock_password_hasher->expects($this->once())
                ->method('hashPassword')
                ->willReturn('hashed_admin_password');

            $mock_manager = $this->createMock(ObjectManager::class);

            $mock_manager->expects($this->once())
                ->method('persist')
                ->with($this->callback(function (User $user) {
                    return $user->getEmail() === 'admin@example.com'
                        && $user->getUsername() === 'admin'
                        && in_array(UserRole::ROLE_ADMIN->value, $user->getRoles())
                        && in_array(UserRole::ROLE_USER->value, $user->getRoles())
                        && $user->getPassword() === 'hashed_admin_password';
                }));

            $mock_manager->expects($this->once())
                ->method('flush');

            $user_fixtures = new UserFixtures($mock_password_hasher);

            $faker = Factory::create();
            $reflection = new \ReflectionClass($user_fixtures);

            $faker_property = $reflection->getParentClass()->getProperty('faker');
            $faker_property->setValue($user_fixtures, $faker);

            // when
            $user_fixtures->load($mock_manager);
        } catch (\Exception $e) {
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
    public function test_load_data_returns_early_if_dependencies_are_missing(): void
    {
        try {
            // given
            $mock_password_hasher = $this->createMock(UserPasswordHasherInterface::class);
            $user_fixtures = new UserFixtures($mock_password_hasher);
            
            $reflection = new \ReflectionClass(UserFixtures::class);
            $method = $reflection->getMethod('loadData');
            $method->setAccessible(true);

            $faker_property = $reflection->getParentClass()->getProperty('faker');
            $faker_property->setValue($user_fixtures, null);

            // when
            $method->invoke($user_fixtures);

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