<?php

namespace App\Domains\CarbonEmissions\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTrainCarbonEmissionsModel extends Model
{
    public $origin;
    public $destination;
    public $passengers;
    public $train_type;
    public $seat_type;
    public $operator_name;
}
