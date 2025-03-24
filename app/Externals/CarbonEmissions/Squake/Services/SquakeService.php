<?php

namespace App\Externals\CarbonEmissions\Squake\Services;

use App\Domains\CarbonEmissions\Models\RequestFlightCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestHotelCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\RequestTrainCarbonEmissionsModel;
use App\Domains\CarbonEmissions\Models\ResponseEmissionsModel;
use App\Externals\CarbonEmissions\Services\CarbonEmissionProviderServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SquakeService implements CarbonEmissionProviderServiceInterface
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.squake.api_key');
        $this->baseUrl = config('services.squake.url');

        // Check if API credentials are configured
        if (empty($this->apiKey)) {
            Log::error('Squake API key is not configured');
        }
        
        if (empty($this->baseUrl)) {
            Log::error('Squake URL is not configured');
            $this->baseUrl = 'https://api.sandbox.squake.earth/v2'; // Default URL if not set
        }
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ],
            'http_errors' => false // Don't throw exceptions for HTTP errors
        ]);
    }

    public function getFlightCarbonEmissionCalculation(RequestFlightCarbonEmissionsModel $request): ResponseEmissionsModel
    {
        $requestData = [
            'expand' => ['items'],
            'items' => [
                [
                    'type' => 'flight',
                    'methodology' => 'GATE4',
                    'origin' => $request->origin,
                    'destination' => $request->destination,
                    'number_of_travelers' => $request->passengers,
                    'booking_class' => $request->class,
                    'airline' => $request->airline,
                    'flight_number' => $request->flight_number,
                    'external_reference' => 'test'
                ]
            ]
        ];

        $response = $this->makeApiRequest('v2/calculations', $requestData);
        return $this->transformToResponseModel($response, 'flight');
    }

    public function getTrainCarbonEmissionCalculation(RequestTrainCarbonEmissionsModel $request): ResponseEmissionsModel
    {
        $requestData = [
            'expand' => ['items'],
            'items' => [
                [
                    'type' => 'train',
                    'methodology' => 'BASE-EMPREINTE',
                    'origin' => $request->origin,
                    'destination' => $request->destination,
                    'number_of_travelers' => $request->passengers,
                    'train_type' => $request->train_type,
                    'seat_type' => $request->seat_type,
                    'operator_name' => $request->operator_name,
                    'country' => 'FR',
                    'external_reference' => 'test'
                ]
            ]
        ];

        $response = $this->makeApiRequest('v2/calculations', $requestData);
        return $this->transformToResponseModel($response, 'train');
    }

    public function getHotelCarbonEmissionCalculation(RequestHotelCarbonEmissionsModel $request): ResponseEmissionsModel
    {
        $requestData = [
            'expand' => ['items'],
            'items' => [
                [
                    'type' => 'hotel',
                    'methodology' => 'HCMI',
                    'hotel_type' => $request->hotel_type,
                    'stars' => $request->stars,
                    'country' => $request->country,
                    'city' => $request->city,
                    'hotel_name' => $request->hotel_name,
                    'code' => $request->code ?? '877089',
                    'code_type' => $request->code_type ?? 'giata',
                    'room_type' => $request->room_type,
                    'number_of_visitors' => $request->number_of_visitors,
                    'number_of_nights' => $request->number_of_nights,
                    'external_reference' => 'test'
                ]
            ]
        ];

        $response = $this->makeApiRequest('v2/calculations', $requestData);
        return $this->transformToResponseModel($response, 'hotel');
    }

    protected function makeApiRequest($endpoint, $data)
    {
        try {
            // Generate and log the equivalent curl command for debugging
            $this->logCurlCommand($endpoint, $data);
            
            // Log the request for debugging
            Log::info('Making Squake API request', [
                'endpoint' => $endpoint,
                'api_key_length' => strlen($this->apiKey),
                'base_url' => $this->baseUrl
            ]);
            
            $response = $this->client->post($endpoint, [
                'json' => $data
            ]);
            
            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            // Log the response status
            Log::info('Squake API response received', [
                'status_code' => $statusCode,
                'success' => $statusCode >= 200 && $statusCode < 300
            ]);
            
            if ($statusCode >= 200 && $statusCode < 300) {
                return $responseBody;
            } else {
                $errorMessage = isset($responseBody['error']) 
                    ? $responseBody['error'] 
                    : 'API error: HTTP ' . $statusCode;
                
                Log::error('Squake API error', [
                    'status_code' => $statusCode,
                    'error' => $errorMessage,
                    'response' => $responseBody
                ]);
                
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Squake API request failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate and log the equivalent curl command for debugging purposes
     * 
     * @param string $endpoint
     * @param array $data
     * @return void
     */
    protected function logCurlCommand($endpoint, $data)
    {
        $fullUrl = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        
        $curlCommand = "curl --location '$fullUrl' \\\n";
        $curlCommand .= "--header 'Content-Type: application/json' \\\n";
        $curlCommand .= "--header 'Authorization: Bearer " . substr($this->apiKey, 0, 4) . "••••" . substr($this->apiKey, -4) . "' \\\n";
        $curlCommand .= "--data '$jsonData'";
        
        Log::debug('Equivalent curl command:', ['curl' => $curlCommand]);
        
        return $curlCommand;
    }

    protected function transformToResponseModel(array $apiResponse, string $type): ResponseEmissionsModel
    {
        $responseModel = new ResponseEmissionsModel();
        
        // Set basic properties from the API response
        $responseModel->carbon_quantity = $apiResponse['carbon_quantity'] ?? 0;
        $responseModel->carbon_unit = $apiResponse['carbon_unit'] ?? 'gram';
        $responseModel->type = $type;
        
        // Get the first item from the response
        $item = $apiResponse['items'][0] ?? [];
        
        // Set distance properties if available
        if (isset($item['distance']) && isset($item['distance_unit'])) {
            $responseModel->distance = $item['distance'];
            $responseModel->distance_unit = $item['distance_unit'];
        }
        
        // Set eco label if available
        if (isset($item['eco_labels']) && !empty($item['eco_labels'])) {
            $responseModel->eco_label = $item['eco_labels'][0]['name'] ?? null;
        }
        
        return $responseModel;
    }
}
