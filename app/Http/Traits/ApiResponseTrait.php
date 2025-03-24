<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    /**
     * Return success response
     *
     * @param mixed $data
     * @param string $message
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $message = 'success', $code = '200')
    {
        if (!$data) {
            return response()->json([
                'code' => $code,
                'message' => $message,
            ]);
        }

        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param mixed $data
     * @param string $code
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message = 'error', $data = null, $code = '400', $statusCode = 400)
    {
        if (!$data) {
            return response()->json([
                'code' => $code,
                'message' => $message,
            ], $statusCode);
        }
        
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
