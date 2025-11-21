<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Country extends Model
{
    public function channels()
{
    return $this->belongsToMany(Channel::class, 'channel_country');
}


    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packages_countries');
    }

}
