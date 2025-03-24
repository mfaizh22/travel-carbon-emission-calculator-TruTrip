<?php

namespace App\Domains\CarbonEmissions\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFlightCarbonEmissionsModel extends Model
{
    public $origin;
    public $destination;
    public $passengers;
    public $class;
    public $airline;
    public $flight_number;
}
