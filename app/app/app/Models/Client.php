<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Client extends Model
{
    use HasFactory;

    /**
     * Имя таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * Атрибуты, которые можно массово заполнять (для методов create/update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'token',
        'is_active',
        'limit_count',
        'final_date',
    ];
    
    /**
     * Атрибуты, которые должны быть приведены к нативным типам.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'limit_count' => 'integer',
        'final_date' => 'datetime',
        // Если поле 'links' будет сериализовано в модели (например, с JSON), 
        // его нужно добавить в $casts. Однако, поскольку оно в отдельной таблице, 
        // мы используем отношения.
    ];
    
    // ------------------------------------
    // Отношения (Relationships)
    // ------------------------------------

    /**
     * Получить все ссылки (links), принадлежащие клиенту.
     */
    public function links(): HasMany
    {
        // Клиент имеет много ссылок (HasMany)
        // Модель ClientLink должна быть создана
        return $this->hasMany(ClientLink::class, 'client_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'clients_packages')
                    ->withTimestamps();
    }

    /**
     * Метод экземпляра: Проверяет и обновляет статус is_active для ТЕКУЩЕГО клиента.
     */
    public function checkAndDeactivateIfExpired(): bool
    {
        // Проверяем, существует ли final_date и истекла ли она
        if ($this->final_date && $this->final_date->isPast()) {
            
            // Если клиент уже неактивен, ничего не делаем
            if (!$this->is_active) {
                return false; 
            }
            
            // Обновляем is_active на false
            $this->is_active = false;
            $this->save();
            
            return true; // Возвращаем true, если статус был изменен
        }
        
        return false; // Возвращаем false, если статус не был изменен
    }

    public static function deactivateExpiredClients(): int
    {
        // Находим всех активных клиентов, чья final_date находится в прошлом
        $updatedCount = self::query()
            ->where('is_active', true)
            ->where('final_date', '<', now()) // Используем Carbon/DB-функции
            ->update(['is_active' => false]);

        return $updatedCount;
    }
   

   
   
}
