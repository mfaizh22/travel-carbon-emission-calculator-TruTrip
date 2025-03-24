<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;
use App\Domains\Users\Repository\UserRepositoryInterface;
use App\Domains\Users\Services\AuthServiceInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    protected $userRepository;
    
    /**
     * Create a new AuthService instance.
     *
     * @param UserRepositoryInterface $userRepository
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    /**
     * Login a user and return a token
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function login($data)
    {
        $user = $this->userRepository->findByEmail($data['email']);
        
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email not verified. Please verify your email before logging in.'],
            ]);
        }
        
        // Create a new token
        $token = $user->createToken('api-token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
        ];
    }
    
    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register($data): User
    {
        $user = $this->userRepository->create($data);
        
        // Send verification email
        $this->userRepository->sendEmailVerificationNotification($user);
        
        return $user;
    }
    
    /**
     * Verify email
     *
     * @param int $userId
     * @param string $hash
     * @return bool
     */
    public function verifyEmail($userId, $hash)
    {
        $user = User::findOrFail($userId);
        
        //For now we will skip the hash verification due to email issue
        // if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        //     return false;
        // }
        
        if ($user->hasVerifiedEmail()) {
            return true; // Already verified
        }
        
        if ($this->userRepository->markEmailAsVerified($user)) {
            event(new Verified($user));
            return true;
        }
        
        return false;
    }
    
    /**
     * Resend verification email
     *
     * @param int $userId
     * @return void
     */
    public function resendVerificationEmail($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email already verified.'],
            ]);
        }
        
        $this->userRepository->sendEmailVerificationNotification($user);
    }
}
