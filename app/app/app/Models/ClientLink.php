<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientLink extends Model
{
    use HasFactory;

    protected $table = 'client_links';
    
    protected $fillable = [
        'client_id',
        'url',
    ];

    /**
     * Получить клиента, которому принадлежит ссылка.
     */
    public function client(): BelongsTo
    {
        // Ссылка принадлежит одному клиенту (BelongsTo)
        return $this->belongsTo(Client::class);
    }
}
