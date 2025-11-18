<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeviceSeeder extends Seeder
{
    /**
     * Запускает заполнение базы данных.
     *
     * @return void
     */
    public function run(): void
    {
        // Создаем 10 тестовых записей
        for ($i = 0; $i < 10; $i++) {
            // 1. Генерируем случайную 8-значную числовую строку
            // STR_PAD_LEFT гарантирует, что строка всегда будет 8 символов (например, 00123456)
            $numeric_id = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            
            // 2. Создаем запись в таблице
            Device::create([
                // Поле 'name' - 8-значная числовая строка
                'name' => $numeric_id,
                
                // Поле 'password' - хешированный вариант той же 8-значной строки
                'password' => $numeric_id,
                
                // Пример заполнения других полей
                'token' => Str::random(32),
                'status' => 1,
            ]);
        }

        $this->command->info('10 клиентов (логин/пароль: 8-значные числа) успешно созданы.');
    }
}