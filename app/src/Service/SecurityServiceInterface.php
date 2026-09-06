<?php

/**
 * Security service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface SecurityServiceInterface.
 */
interface SecurityServiceInterface
{
    /**
     * Register a new user.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain password
     */
    public function registerUser(User $user, string $plainPassword): void;
}
