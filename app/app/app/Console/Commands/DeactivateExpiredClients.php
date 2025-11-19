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
    protected $description = 'Command description';

     protected $portal;

       public function __construct(IptvPortal $device)
    {
      $this->portal = $device;
    }


   
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем проверку и деактивацию клиентов...');

        // ⭐️ Вызываем статический метод из модели Client
        $count = Client::deactivateExpiredClients(); 
        $devices = Device:: getAllExpireDevices();
        $device_count =  Device::deactivateExpiredDevices();
        if($device_count > 0){
          
           foreach ($devices as $v){
            $this->portal->disable_account($v, true);
          }   
        }

        if ($count > 0) {
            $this->info("✅ Успешно деактивировано {$count} клиентов.");
        } else {
            $this->info("ℹ️ Не найдено клиентов для деактивации.");
        }
    }
}
