# Carbon Emission Calculator API

A Laravel-based API for calculating carbon emissions for various transportation methods and accommodations using the Squake API. This service provides accurate carbon footprint calculations for flights, trains, and hotel stays.

## Features

- **Multiple Transportation Types**: Calculate carbon emissions for:
  - Flights (domestic and international)
  - Train journeys
  - Hotel stays
- **Secure Authentication**: API endpoints protected with Laravel Sanctum
- **Comprehensive Error Handling**: Detailed error responses for invalid requests
- **Integration with Squake API**: Leverages Squake's advanced carbon calculation methodologies

## Requirements

- PHP 8.1+ (or Docker for Sail)
- Composer
- MySQL or compatible database
- Squake API credentials

## Installation

### Option 1: Standard Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/carbon-emission-calculator-api.git
   cd carbon-emission-calculator-api
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and configure your settings:
   ```bash
   cp .env.example .env
   ```

4. Configure your database and Squake API credentials in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=carbon_emissions
   DB_USERNAME=root
   DB_PASSWORD=

   SQUAKE_API_KEY=your_api_key_here
   SQUAKE_URL=https://api.sandbox.squake.earth/v2
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

### Option 2: Using Laravel Sail (Docker)

Laravel Sail provides a lightweight command-line interface for interacting with Laravel's default Docker development environment.

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/carbon-emission-calculator-api.git
   cd carbon-emission-calculator-api
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Install Sail dependencies:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php81-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. Configure your Squake API credentials in the `.env` file:
   ```
   SQUAKE_API_KEY=your_api_key_here
   SQUAKE_URL=https://api.sandbox.squake.earth/v2
   ```

5. Start Sail:
   ```bash
   ./vendor/bin/sail up -d
   ```

6. Generate application key:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

7. Run migrations:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

### Sail Commands

Here are the common Laravel Sail commands organized by task:

1. **Container Management**:
   ```bash
   # Start all containers
   ./vendor/bin/sail up -d
   
   # Stop all containers
   ./vendor/bin/sail down
   
   # Restart containers
   ./vendor/bin/sail restart
   
   # View container status
   ./vendor/bin/sail ps
   ```

2. **Artisan Commands**:
   ```bash
   # Run migrations
   ./vendor/bin/sail artisan migrate
   
   # Generate key
   ./vendor/bin/sail artisan key:generate
   
   # Clear cache
   ./vendor/bin/sail artisan cache:clear
   
   # Create a controller
   ./vendor/bin/sail artisan make:controller YourController
   ```

3. **Dependency Management**:
   ```bash
   # Install dependencies
   ./vendor/bin/sail composer install
   
   # Update dependencies
   ./vendor/bin/sail composer update
   
   # Add a package
   ./vendor/bin/sail composer require package/name
   ```

4. **Testing**:
   ```bash
   # Run all tests
   ./vendor/bin/sail test
   
   # Run specific test file
   ./vendor/bin/sail test --filter=CarbonEmissionCalculationTest
   
   # Run tests with coverage report
   ./vendor/bin/sail test --coverage
   ```

5. **Database Access**:
   ```bash
   # Access MySQL CLI
   ./vendor/bin/sail mysql
   
   # Run database migrations fresh with seeding
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

6. **Logs and Debugging**:
   ```bash
   # View Laravel logs
   ./vendor/bin/sail logs
   
   # View logs for specific container
   ./vendor/bin/sail logs mysql
   ```

You can create a Bash alias for Sail to simplify commands:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

After adding this alias, you can simply use `sail` instead of `./vendor/bin/sail`.

## API Documentation

### Authentication

All carbon emission calculation endpoints require authentication using Laravel Sanctum.

#### Register a new user

```
POST /api/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

#### Login

```
POST /api/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": "2023-01-01T00:00:00.000000Z",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  },
  "token": "your_api_token"
}
```

### Carbon Emission Calculations

#### Calculate Flight Carbon Emissions

```
POST /api/v1/carbon-emissions/calculate
```

**Headers:**
```
Authorization: Bearer your_api_token
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "type": "flight",
  "departure_airport": "JFK",
  "arrival_airport": "LAX",
  "passengers": 2,
  "class": "economy",
  "airline": "AA",
  "flight_number": "AA123"
}
```

**Response:**
```json
{
  "data": {
    "type": "flight",
    "carbon_quantity": 1234.56,
    "carbon_unit": "gram",
    "distance": {
      "value": 4000,
      "unit": "km"
    },
    "timestamp": "2023-01-01T00:00:00.000000Z",
    "provider": "Squake"
  }
}
```

#### Calculate Train Carbon Emissions

```
POST /api/v1/carbon-emissions/calculate
```

**Request Body:**
```json
{
  "type": "train",
  "departure_station": "PAR",
  "arrival_station": "LYO",
  "passengers": 1,
  "train_type": "high_speed",
  "seat_type": "second_class",
  "operator_name": "SNCF",
  "country": "FR"
}
```

#### Calculate Hotel Carbon Emissions

```
POST /api/v1/carbon-emissions/calculate
```

**Request Body:**
```json
{
  "type": "hotel",
  "hotel_type": "hotel",
  "stars": 4,
  "country": "FR",
  "city": "Paris",
  "hotel_name": "Example Hotel",
  "code": "877089",
  "code_type": "giata",
  "room_type": "standard",
  "number_of_visitors": 2,
  "number_of_nights": 3
}
```

### Error Responses

The API returns appropriate HTTP status codes and error messages:

- **400 Bad Request**: Invalid input parameters
- **401 Unauthorized**: Missing or invalid authentication
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server-side errors

Example error response:
```json
{
  "error": {
    "code": "422",
    "message": "Type is required"
  }
}
```

## Implementation Details

### Architecture

The application follows a domain-driven design approach with the following structure:

- **Domains**: Core business logic organized by domain
  - Users: Authentication and user management
  - CarbonEmissions: Carbon calculation services and models
- **Externals**: Integration with external services
  - Squake: API client for Squake carbon calculation service
- **Http**: Controllers and middleware for handling HTTP requests

### Methodologies

The API uses the following methodologies from Squake:

- **Flight**: GATE4 methodology
- **Train**: BASE-EMPREINTE methodology
- **Hotel**: HCMI methodology

## Testing

Run the test suite with:

```bash
php artisan test
```

The test suite includes:
- Unit tests for core services
- Integration tests for API endpoints
- Mock tests for external API calls

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
