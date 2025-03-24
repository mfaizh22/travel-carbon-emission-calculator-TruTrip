<?php

namespace App\Domains\Users\Repository;

use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    
    /**
     * Mark email as verified for a user
     *
     * @param User $user
     * @return bool
     */
    public function markEmailAsVerified(User $user): bool
    {
        return $user->markEmailAsVerified();
    }
    
    /**
     * Create a new email verification notification
     *
     * @param User $user
     * @return void
     */
    public function sendEmailVerificationNotification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}