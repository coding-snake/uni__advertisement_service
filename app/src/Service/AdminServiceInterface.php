<?php

/**
 * Admin service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface AdminServiceInterface.
 */
interface AdminServiceInterface
{
    /**
     * Save user entity.
     *
     * @param User $user User entity
     */
    public function saveUser(User $user): void;

    /**
     * Change user password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain password
     */
    public function changeUserPassword(User $user, string $plainPassword): void;
}
