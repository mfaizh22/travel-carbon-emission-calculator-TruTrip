<?php

namespace App\Domains\CarbonEmissions\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseEmissionsModel extends Model
{
    public $carbon_quantity;
    public $carbon_unit;
    public $distance;
    public $distance_unit;
    public $type;
    public $eco_label;
}
