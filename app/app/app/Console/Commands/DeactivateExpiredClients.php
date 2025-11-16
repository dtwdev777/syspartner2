<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;

class DeactivateExpiredClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем проверку и деактивацию клиентов...');

        // ⭐️ Вызываем статический метод из модели Client
        $count = Client::deactivateExpiredClients(); 

        if ($count > 0) {
            $this->info("✅ Успешно деактивировано {$count} клиентов.");
        } else {
            $this->info("ℹ️ Не найдено клиентов для деактивации.");
        }
    }
}
