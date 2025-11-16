<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Удаляем существующие записи, чтобы избежать дубликатов (опционально)
        // DB::table('users')->truncate();

        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@sample.com',
            'password' => Hash::make('demo'), // Всегда хешируйте пароль!
            'email_verified_at' => Carbon::now(), // Указываем, что почта подтверждена
            
            // Заполняем остальные поля, которые вы видите в структуре (image_8a813f.png):
            
            // Поля для двухфакторной аутентификации (оставляем NULL, если не настроено)
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,

            // Токен "запомнить меня"
            'remember_token' => null, 
            
            // Временные метки
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Можете добавить здесь больше пользователей
    }
}