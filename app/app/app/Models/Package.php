<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

     public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'clients_packages')
                    ->withTimestamps();
    }
   
}
