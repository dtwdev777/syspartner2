<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
     protected $fillable = ['name', 'is_active'];
     protected $casts = [
        'is_active' => 'boolean',
     
    ];

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'packages_countries');
    }
   
}
