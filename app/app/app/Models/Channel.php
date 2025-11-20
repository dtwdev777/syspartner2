<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Channel extends Model
{
    use HasFactory;

    /**
     * Поля, разрешенные для массового заполнения (Mass Assignment).
     * Соответствуют полям в миграции, кроме 'id' и меток времени.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'title',
        'link',
    ];

    /**
     * Определяет отношение "многие ко многим" (Many-to-Many) со странами (Country).
     * Использует автоматически сгенерированную связующую таблицу 'channel_country'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function countries(): BelongsToMany
    {
        // Laravel автоматически предполагает имя связующей таблицы как 'channel_country' 
        // и внешний ключ 'country_id'.
        return $this->belongsToMany(Country::class);
    }
}