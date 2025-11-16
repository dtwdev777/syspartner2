<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Создание 5 тестовых клиентов
        $clients = [];
        for ($i = 1; $i <= 5; $i++) {
            $clients[] = [
                'name' => 'Test Client ' . $i,
                'token' => Str::random(20), // Генерация уникального токена
                'is_active' => $i % 2 == 0, // Четные клиенты активны
                'limit_count' => $i * 100,
                'final_date' => now()->addDays($i * 30), // Дата в будущем
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('clients')->insert($clients);

        // 2. Создание ссылок для этих клиентов
        
        // Получаем ID только что созданных клиентов
        $clientIds = DB::table('clients')->pluck('id');
        $links = [];
        
        foreach ($clientIds as $clientId) {
            // Добавляем 3 ссылки для каждого клиента
            for ($j = 1; $j <= 3; $j++) {
                $links[] = [
                    'client_id' => $clientId,
                    'url' => 'https://example.com/client/' . $clientId . '/link/' . $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('client_links')->insert($links);

        $this->command->info('Clients and their links seeded successfully!');
    }
}
