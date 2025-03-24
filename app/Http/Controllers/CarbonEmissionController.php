<?php

namespace App\Http\Controllers;

use App\Domains\CarbonEmissions\Services\CarbonEmissionServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Exception;

class CarbonEmissionController extends Controller
{
    use ApiResponseTrait;

    protected $carbonEmissionService;

    public function __construct(CarbonEmissionServiceInterface $carbonEmissionService)
    {
        $this->carbonEmissionService = $carbonEmissionService;
    }

    public function calculate(Request $request)
    {
        try {
            // Basic validation - we'll implement detailed validation later
            if (!$request->has('type')) {
                return $this->errorResponse('Type is required', null, '422', 422);
            }

            // Calculate carbon emissions
            $result = $this->carbonEmissionService->getCarbonEmissionCalculation($request->all());
            
            // Return success response
            return $this->successResponse($result);
        } catch (Exception $e) {
            return $this->errorResponse(
                'An error occurred while calculating carbon emissions',
                config('app.debug') ? $e->getMessage() : null,
                '500',
                500
            );
        }
    }
}
