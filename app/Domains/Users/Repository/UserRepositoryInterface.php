<?php

namespace App\Domains\Users\Repository;

use App\Domains\Users\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;
    
    /**
     * Mark email as verified for a user
     *
     * @param User $user
     * @return bool
     */
    public function markEmailAsVerified(User $user): bool;
    
    /**
     * Create a new email verification notification
     *
     * @param User $user
     * @return void
     */
    public function sendEmailVerificationNotification(User $user): void;
}
