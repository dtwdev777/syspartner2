<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Device;
use App\Services\IptvPortal;

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
    protected $description = 'Деактивирует просроченных клиентов и связанные устройства.';

    protected $portal;

    /**
     * Исправленный конструктор с вызовом родительского метода.
     *
     * @param IptvPortal $device
     */
    public function __construct(IptvPortal $device)
    {
       
        parent::__construct(); 
        $this->portal = $device;
    }

    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем проверку и деактивацию...');

        // 1. Деактивация основных клиентов (Предполагается, что Client::deactivateExpiredClients() 
        //    обновляет статус в БД и возвращает количество).
        $client_count = Client::deactivateExpiredClients(); 

        // 2. Получаем коллекцию просроченных устройств. (ТОЛЬКО ЧТЕНИЕ)
        //    Предполагается, что Device::getAllExpireDevices() возвращает коллекцию.
        $devices = Device::getAllExpireDevices();
        $device_count = 0;

        if ($devices->isNotEmpty()) {
            $device_total = $devices->count();
            $this->info(" Обнаружено {$device_total} просроченных устройств. Начинаем обработку...");

            // 3. Сначала выполняем внешнюю деактивацию через портал для каждого устройства
            foreach ($devices as $device) {
                // $v - это объект модели Device, который передается в сервис портала
                $this->portal->disable_account($device, false);
            }

            // 4. После успешного вызова внешнего сервиса, 
            //    массово обновляем статус в нашей базе данных.
            //    Предполагается, что Device::deactivateExpiredDevices() обновляет БД и возвращает count.
            $device_count = Device::deactivateExpiredDevices(); 

            $this->info(" Успешно обработано {$device_total} устройств через Портал.");
            $this->info(" Обновлен статус {$device_count} устройств в базе данных.");

        } else {
            $this->info(" Не найдено устройств для деактивации.");
        }

        $this->newLine(); // Добавим пустую строку для читабельности

        // Результаты по клиентам
        if ($client_count > 0) {
            $this->info(" Успешно деактивировано {$client_count} клиентов.");
        } else {
            $this->info("Не найдено клиентов для деактивации.");
        }
    }
}