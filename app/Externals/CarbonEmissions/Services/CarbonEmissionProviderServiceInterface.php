<?php

namespace App\Externals\CarbonEmissions\Services;

use App\Domains\CarbonEmissions\Models\RequestTrainCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestFlightCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestHotelCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\ResponseEmissionsModel;

interface CarbonEmissionProviderServiceInterface
{
    public function getTrainCarbonEmissionCalculation(RequestTrainCarbonEmissionsModel $request): ResponseEmissionsModel;

    public function getFlightCarbonEmissionCalculation(RequestFlightCarbonEmissionsModel $request): ResponseEmissionsModel;

    public function getHotelCarbonEmissionCalculation(RequestHotelCarbonEmissionsModel $request): ResponseEmissionsModel;
}
