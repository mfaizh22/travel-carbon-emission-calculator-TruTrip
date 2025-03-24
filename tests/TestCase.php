<?php

namespace Tests;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs($user, $guard = null)
    {
        if ($guard === 'sanctum' || $guard === null) {
            Sanctum::actingAs($user, ['*']);
            return $this;
        }
        
        return parent::actingAs($user, $guard);
    }
    
    /**
     * Create and authenticate a user for API testing
     *
     * @param array $attributes
     * @return $this
     */
    protected function authenticateUser($attributes = [])
    {
        $user = User::factory()->create(array_merge([
            'email_verified_at' => now(),
        ], $attributes));
        
        return $this->actingAs($user, 'sanctum');
    }
}
