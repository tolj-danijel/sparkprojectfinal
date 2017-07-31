<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table='cars';
    protected $fillable=[
        'make',
        'model',
        'year',
        'is_rented',
    ];
  
}