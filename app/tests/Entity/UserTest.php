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
     * Instead of a bunch of small functions, just do one massive User entity
     */
    public function testGetAndSet(): void
    {
        // given
        $user = new User();

        // when
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $user->setUsername('test');

        // then
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('test@example.com', $user->getUserIdentifier());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals('test', $user->getUsername());
        $this->assertNull($user->getId());
    }

    /**
     * Test roles
     */
    public function testRoles(): void
    {
        // given
        $user = new User();

        // when
        // then
        $this->assertContains(UserRole::ROLE_USER->value, $user->getRoles());

        // when
        $user->setRoles([UserRole::ROLE_ADMIN->value]);

        // then
        $roles = $user->getRoles();
        $this->assertContains(UserRole::ROLE_ADMIN->value, $roles);
        $this->assertCount(2, $roles);
    }

    /**
     * Test erase credentials
     */
    public function testEraseCredentials(): void
    {
        // given
        $user = new User();

        // when
        $user->eraseCredentials();

        // then
        $this->assertTrue(true);
    }
}