<?php

/**
 * User entity tests.
 */

namespace App\Tests\Entity;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    /**
     * Test get and set.
     */
    public function testGetAndSet(): void
    {
        try {
            // given
            $user = new User();

            // when
            $user->setEmail('test@example.com');
            $user->setPassword('password_1');
            $user->setUsername('test');

            // then
            $this->assertEquals('test@example.com', $user->getEmail());
            $this->assertEquals('test@example.com', $user->getUserIdentifier());
            $this->assertEquals('password_1', $user->getPassword());
            $this->assertEquals('test', $user->getUsername());
            $this->assertNull($user->getId());
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test roles.
     */
    public function testRoles(): void
    {
        try {
            // given
            $user = new User();

            // then
            $this->assertContains(UserRole::ROLE_USER->value, $user->getRoles());

            // when
            $user->setRoles([UserRole::ROLE_ADMIN->value]);

            // then
            $roles = $user->getRoles();
            $this->assertContains(UserRole::ROLE_ADMIN->value, $roles);
            $this->assertContains(UserRole::ROLE_USER->value, $roles);
            $this->assertCount(2, $roles);
        } catch (\Exception $e) {
            dd([
                'Error' => $e->getMessage(),
                'File'  => $e->getFile(),
                'Line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Test erase credentials.
     */
    public function testEraseCredentials(): void
    {
        try {
            // given
            $user = new User();

            // when
            $user->eraseCredentials();

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
