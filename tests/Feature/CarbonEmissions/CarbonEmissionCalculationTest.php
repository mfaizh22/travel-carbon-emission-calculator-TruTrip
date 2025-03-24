<?php

namespace Tests\Feature\CarbonEmissions;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

class CarbonEmissionCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configure the base URL for the mock
        $baseUrl = config('services.squake.url');
        
        // Default mock response for all endpoints
        Http::fake([
            "{$baseUrl}/v2/calculations" => Http::response($this->getMockFlightResponse(), 200),
        ]);
    }
    
    /** @test */
    public function it_can_calculate_flight_carbon_emissions()
    {
        // Mock the flight API response
        Http::fake([
            config('services.squake.url') . '/v2/calculations' => Http::response($this->getMockFlightResponse(), 200),
        ]);
        
        $this->authenticateUser();
        
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'flight',
            'departure_airport' => 'JFK',
            'arrival_airport' => 'LAX',
            'passengers' => 2,
            'class' => 'economy',
            'airline' => 'AA',
            'flight_number' => 'AA123',
        ]);
        
        // Dump the response content for debugging
        dump($response->status(), $response->getContent());
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'carbon_quantity',
                    'carbon_unit',
                    'type',
                ]
            ]);
            
        // Verify the carbon quantity matches our mock
        $this->assertEquals(1234.56, $response->json('data.carbon_quantity'));
    }
    
    /** @test */
    public function it_handles_invalid_flight_parameters()
    {
        $this->authenticateUser();
        
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'flight',
            // Missing required parameters
        ]);
        
        $response->assertStatus(422);
    }
    
    /** @test */
    public function it_can_calculate_train_carbon_emissions()
    {
        // Mock the train API response
        Http::fake([
            config('services.squake.url') . '/v2/calculations' => Http::response($this->getMockTrainResponse(), 200),
        ]);
        
        $this->authenticateUser();
        
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'train',
            'departure_station' => 'PAR',
            'arrival_station' => 'LYO',
            'passengers' => 1,
            'train_type' => 'high_speed',
            'seat_type' => 'second_class',
            'operator_name' => 'SNCF',
            'country' => 'FR',
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'carbon_quantity',
                    'carbon_unit',
                    'type',
                ]
            ]);
            
        // Verify the carbon quantity matches our mock
        $this->assertEquals(456.78, $response->json('data.carbon_quantity'));
    }
    
    /** @test */
    public function it_can_calculate_hotel_carbon_emissions()
    {
        // Mock the hotel API response
        Http::fake([
            config('services.squake.url') . '/v2/calculations' => Http::response($this->getMockHotelResponse(), 200),
        ]);
        
        $this->authenticateUser();
        
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'hotel',
            'hotel_type' => 'hotel',
            'stars' => 4,
            'country' => 'FR',
            'city' => 'Paris',
            'hotel_name' => 'Example Hotel',
            'code' => '877089',
            'code_type' => 'giata',
            'room_type' => 'standard',
            'number_of_visitors' => 2,
            'number_of_nights' => 3,
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'carbon_quantity',
                    'carbon_unit',
                    'type',
                ]
            ]);
            
        // Verify the carbon quantity matches our mock
        $this->assertEquals(789.12, $response->json('data.carbon_quantity'));
    }
    
    /** @test */
    public function it_handles_squake_api_error()
    {
        // Mock an API error response
        Http::fake([
            config('services.squake.url') . '/v2/calculations' => Http::response([
                'error' => 'Invalid API key',
            ], 403),
        ]);
        
        $this->authenticateUser();
        
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'flight',
            'departure_airport' => 'JFK',
            'arrival_airport' => 'LAX',
            'passengers' => 2,
            'class' => 'economy',
            'airline' => 'AA',
            'flight_number' => 'AA123',
        ]);
        
        $response->assertStatus(500)
            ->assertJsonFragment([
                'message' => 'An error occurred while calculating carbon emissions'
            ]);
    }
    
    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'flight',
            'departure_airport' => 'JFK',
            'arrival_airport' => 'LAX',
            'passengers' => 2,
            'class' => 'economy',
            'airline' => 'AA',
            'flight_number' => 'AA123',
        ]);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function it_can_access_the_api()
    {
        // Test the basic API endpoint without authentication
        $response = $this->getJson('/test');
        $response->assertStatus(200);
        
        // Now test with authentication
        $this->authenticateUser();
        $response = $this->getJson('/user');
        $response->assertStatus(200);
        
        // Try accessing the carbon emissions endpoint directly
        $response = $this->postJson('/api/v1/carbon-emissions/calculate', [
            'type' => 'flight',
            'departure_airport' => 'JFK',
            'arrival_airport' => 'LAX',
            'passengers' => 2,
            'class' => 'economy',
            'airline' => 'AA',
            'flight_number' => 'AA123',
        ]);
        
        // Dump response for debugging
        dump($response->status(), $response->getContent());
        
        // Dump the available routes to debug
        $routes = Route::getRoutes();
        $routeList = [];
        
        foreach ($routes as $route) {
            if (strpos($route->uri, 'carbon-emissions') !== false || 
                strpos($route->uri, 'api/v1') !== false) {
                $routeList[] = [
                    'uri' => $route->uri,
                    'methods' => $route->methods,
                ];
            }
        }
        
        // Output the routes for debugging
        dump($routeList);
    }
    
    /** @test */
    public function debug_route_registration()
    {
        // Get all routes
        $routes = Route::getRoutes();
        
        // Find our specific route
        $targetRoute = null;
        foreach ($routes as $route) {
            if (strpos($route->uri, 'carbon-emissions/calculate') !== false) {
                $targetRoute = $route;
                break;
            }
        }
        
        // Output route information
        if ($targetRoute) {
            $this->assertTrue(true, 'Route found: ' . $targetRoute->uri);
            dump([
                'uri' => $targetRoute->uri,
                'methods' => $targetRoute->methods,
                'middleware' => $targetRoute->middleware(),
                'action' => $targetRoute->getActionName(),
            ]);
        } else {
            $this->fail('Route not found');
        }
    }
    
    // Helper methods to generate mock responses
    
    private function getMockFlightResponse()
    {
        return [
            'carbon_quantity' => 1234.56,
            'carbon_unit' => 'gram',
            'items' => [
                [
                    'type' => 'flight',
                    'methodology' => 'GATE4', 
                    'origin' => 'JFK',
                    'destination' => 'LAX',
                    'number_of_travelers' => 2,
                    'booking_class' => 'economy',
                    'airline' => 'AA',
                    'flight_number' => 'AA123',
                    'external_reference' => 'test', 
                    'carbon_quantity' => 1234.56,
                    'carbon_unit' => 'gram',
                    'details' => [
                        'distance' => 4000,
                        'distance_unit' => 'km',
                    ]
                ]
            ]
        ];
    }
    
    private function getMockTrainResponse()
    {
        return [
            'carbon_quantity' => 456.78,
            'carbon_unit' => 'gram',
            'items' => [
                [
                    'type' => 'train',
                    'methodology' => 'BASE-EMPREINTE', 
                    'origin' => 'PAR',
                    'destination' => 'LYO',
                    'number_of_travelers' => 1,
                    'train_type' => 'high_speed',
                    'seat_type' => 'second_class',
                    'operator_name' => 'SNCF',
                    'country' => 'FR', 
                    'external_reference' => 'test', 
                    'carbon_quantity' => 456.78,
                    'carbon_unit' => 'gram',
                    'details' => [
                        'distance' => 400,
                        'distance_unit' => 'km',
                    ]
                ]
            ]
        ];
    }
    
    private function getMockHotelResponse()
    {
        return [
            'carbon_quantity' => 789.12,
            'carbon_unit' => 'gram',
            'items' => [
                [
                    'type' => 'hotel',
                    'methodology' => 'HCMI', 
                    'hotel_type' => 'hotel',
                    'stars' => 4,
                    'country' => 'FR',
                    'city' => 'Paris',
                    'hotel_name' => 'Example Hotel',
                    'code' => '877089', 
                    'code_type' => 'giata', 
                    'room_type' => 'standard',
                    'number_of_visitors' => 2,
                    'number_of_nights' => 3,
                    'external_reference' => 'test', 
                    'carbon_quantity' => 789.12,
                    'carbon_unit' => 'gram',
                    'details' => [
                        'nights' => 3,
                        'visitors' => 2,
                    ]
                ]
            ]
        ];
    }
}
