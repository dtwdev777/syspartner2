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

    protected $casts = [
        'status' => 'boolean',
      
        'final_date' => 'datetime',
        // Если поле 'links' будет сериализовано в модели (например, с JSON), 
        // его нужно добавить в $casts. Однако, поскольку оно в отдельной таблице, 
        // мы используем отношения.
    ];

    


    /**
     * Метод экземпляра: Проверяет и обновляет статус is_active для ТЕКУЩЕГО клиента.
     */
    public function checkAndDeactivateIfExpired(): bool
    {
        // Проверяем, существует ли final_date и истекла ли она
        if ($this->final_date && $this->final_date->isPast()) {
            
            // Если клиент уже неактивен, ничего не делаем
            if (!$this->status) {
                return false; 
            }
            
            // Обновляем is_active на false
            $this->status = false;
            $this->save();
            
            return true; // Возвращаем true, если статус был изменен
        }
        
        return false; // Возвращаем false, если статус не был изменен
    }

    public static function deactivateExpiredDevices(): int
    {
        // Находим всех активных клиентов, чья final_date находится в прошлом
        $updatedCount = self::query()
            ->where('status', true)
            ->where('final_date', '<', now()) // Используем Carbon/DB-функции
            ->update(['status' => false]);


        return $updatedCount;
    }

    public static function getAllExpireDevices(){
       return self::query()->where('status', true)->where('final_date', '<', now())->pluck('name'); 
    }

    

   

   

}
