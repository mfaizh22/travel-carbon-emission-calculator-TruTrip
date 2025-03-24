<?php

namespace App\Domains\CarbonEmissions\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHotelCarbonEmissionsModel extends Model
{
    public $hotel_type;
    public $stars;
    public $country;
    public $city;
    public $hotel_name;
    public $room_type;
    public $number_of_visitors;
    public $number_of_nights;
}
