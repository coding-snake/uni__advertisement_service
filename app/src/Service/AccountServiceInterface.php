<?php

/**
 * Account service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface AccountServiceInterface.
 */
interface AccountServiceInterface
{
    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Change user password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain password
     */
    public function changePassword(User $user, string $plainPassword): void;
}
