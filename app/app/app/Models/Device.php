<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Device extends Model
{
    
    protected $fillable = [
        'name',
        'password',
        'token',
        'status',
        'final_date',
        'tariff_id'
    ];

    

   

   

}
