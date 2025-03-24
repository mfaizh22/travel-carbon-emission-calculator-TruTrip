<?php

namespace App\Http\Controllers;

use App\Domains\Users\Services\AuthServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
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
     * Verify email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        // For now we will skip the signature validation
        // if (!$request->hasValidSignature()) {
        //     return $this->errorResponse('Invalid verification link or link expired.', null, '403', 403);
        // }
        
        $userId = $request->route('id');
        $hash = $request->route('hash');
        
        if ($this->authService->verifyEmail($userId, $hash)) {
            return $this->successResponse(null, 'Email verified successfully.');
        }
        
        return $this->errorResponse('Email verification failed.');
    }
    
    /**
     * Resend verification notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        
        try {
            $this->authService->resendVerificationEmail($request->user_id);
            return $this->successResponse(null, 'Verification link sent!');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
