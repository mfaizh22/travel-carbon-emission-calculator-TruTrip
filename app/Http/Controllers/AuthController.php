<?php

namespace App\Http\Controllers;

use App\Domains\Users\Services\AuthServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    protected $authService;
    
    /**
     * Create a new controller instance.
     *
     * @param AuthServiceInterface $authService
     * @return void
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * Handle user login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        try {
            $result = $this->authService->login($validated);
            return $this->successResponse($result, 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, '401', 401);
        }
    }
    
    /**
     * Handle user registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        try {
            $result = $this->authService->register($validated);
            return $this->successResponse($result, 'Registration successful. Please check your email to verify your account.', '201');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
