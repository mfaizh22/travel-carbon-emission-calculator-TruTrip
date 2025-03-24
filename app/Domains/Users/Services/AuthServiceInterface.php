<?php

namespace App\Domains\Users\Services;

interface AuthServiceInterface
{
    /**
     * Login a user and return a token
     *
     * @param array $data
     * @return array
     */
    public function login($data);
    
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register($data);
    
    /**
     * Verify email
     *
     * @param int $userId
     * @param string $hash
     * @return bool
     */
    public function verifyEmail($userId, $hash);
    
    /**
     * Resend verification email
     *
     * @param int $userId
     * @return void
     */
    public function resendVerificationEmail($userId);
}
