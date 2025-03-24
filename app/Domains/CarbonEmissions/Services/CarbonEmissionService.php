<?php

namespace App\Domains\CarbonEmissions\Services;

use App\Domains\CarbonEmissions\Models\RequestFlightCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestHotelCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestTrainCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Services\CarbonEmissionServiceInterface;
use App\Externals\CarbonEmissions\Services\CarbonEmissionProviderServiceInterface;

class CarbonEmissionService implements CarbonEmissionServiceInterface
{
    protected $carbonEmissionProvider;

    public function __construct(CarbonEmissionProviderServiceInterface $carbonEmissionProvider)
    {
        $this->carbonEmissionProvider = $carbonEmissionProvider;
    }

    public function getCarbonEmissionCalculation($data)
    {
        // Process the data based on the type
        $type = $data['type'] ?? '';

        switch ($type) {
            case 'flight':
                $requestModel = new RequestFlightCarbonEmissionsModel();
                $requestModel->origin = $data['departure_airport'] ?? '';
                $requestModel->destination = $data['arrival_airport'] ?? '';
                $requestModel->passengers = $data['passengers'] ?? 1;
                $requestModel->class = $data['class'] ?? 'economy';
                $requestModel->airline = $data['airline'] ?? '';
                $requestModel->flight_number = $data['flight_number'] ?? '';
                
                $result = $this->carbonEmissionProvider->getFlightCarbonEmissionCalculation($requestModel);
                break;
                
            case 'train':
                $requestModel = new RequestTrainCarbonEmissionsModel();
                $requestModel->origin = $data['departure_station'] ?? '';
                $requestModel->destination = $data['arrival_station'] ?? '';
                $requestModel->passengers = $data['passengers'] ?? 1;
                $requestModel->train_type = $data['train_type'] ?? 'high_speed';
                $requestModel->seat_type = $data['seat_type'] ?? 'second_class';
                $requestModel->operator_name = $data['operator_name'] ?? '';
                
                $result = $this->carbonEmissionProvider->getTrainCarbonEmissionCalculation($requestModel);
                break;
                
            case 'hotel':
                $requestModel = new RequestHotelCarbonEmissionsModel();
                $requestModel->hotel_type = $data['hotel_type'] ?? 'suburban_location';
                $requestModel->stars = $data['hotel_class'] ?? 3;
                $requestModel->country = $data['country'] ?? '';
                $requestModel->city = $data['city'] ?? '';
                $requestModel->hotel_name = $data['hotel_name'] ?? '';
                $requestModel->code = $data['hotel_code'] ?? '';
                $requestModel->code_type = $data['code_type'] ?? 'giata';
                $requestModel->room_type = $data['room_type'] ?? 'single';
                $requestModel->number_of_visitors = $data['guests'] ?? 1;
                
                $result = $this->carbonEmissionProvider->getHotelCarbonEmissionCalculation($requestModel);
                break;
                
            default:
                throw new \InvalidArgumentException('Unsupported carbon emission calculation type: ' . $type);
        }

        // Transform the result to the expected format
        return [
            'type' => $type,
            'emissions' => [
                'quantity' => $result->carbon_quantity,
                'unit' => $result->carbon_unit
            ],
            'distance' => isset($result->distance) ? [
                'value' => $result->distance,
                'unit' => $result->distance_unit
            ] : null,
            'eco_label' => $result->eco_label ?? null,
            'timestamp' => now()->toIso8601String(),
            'provider' => 'Squake'
        ];
    }
}
