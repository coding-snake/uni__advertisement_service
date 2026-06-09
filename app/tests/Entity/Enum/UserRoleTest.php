<?php

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function testLabelReturnsCorrectTranslationKey(): void
    {
        // given
        // when
        $userLabel = UserRole::ROLE_USER->label();
        $adminLabel = UserRole::ROLE_ADMIN->label();

        // then
        $this->assertSame('label.role_user', $userLabel);
        $this->assertSame('label.role_admin', $adminLabel);
    }

    public function testEnumBackedValues(): void
    {
        // given
        // when
        $userValue = UserRole::ROLE_USER->value;
        $adminValue = UserRole::ROLE_ADMIN->value;

        // then
        $this->assertSame('ROLE_USER', $userValue);
        $this->assertSame('ROLE_ADMIN', $adminValue);
    }
}