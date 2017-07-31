<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentedCar extends Model
{
    
    protected $fillable=[
        'user_id',
        'car_id',
        'date_rented',
        'date_returned',
        'price'
    ];
  
}