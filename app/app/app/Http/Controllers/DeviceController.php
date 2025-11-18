<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IptvPortal;
use Inertia\Inertia;
use App\Models\Device;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    protected $portal;

    public function __construct(IptvPortal $device)
    {
      $this->portal = $device;
    }

    public function index(Device $device){
      
        return Inertia::render('DeviceList', [
        'devices' => $device->all() ,
    ]);
    }

    public function create(){
     
      return Inertia::render('DeviceCreate', [
        'token' => '',
    ]);
    }

    public function store(Request $request){
          // 1. Валидация входящих данных
        // В реальном приложении рекомендуется использовать Form Request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:devices,name'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            // Проверяем, что дата либо null, либо соответствует формату YYYY-MM-DD
            'validUntilDate' => ['nullable', 'date_format:Y-m-d'],
            'isEnabled' => ['required', 'boolean'],
        ]);

        // 2. Создание новой записи устройства
        $device = Device::create([
            'name' => $validated['name'],
            // Пароль обязательно должен быть хеширован перед сохранением
            'password' => $validated['password'],
            // Маппинг 'validUntilDate' на 'final_date' (или другое поле в вашей БД)
            'final_date' => $validated['validUntilDate'],
            // Маппинг 'isEnabled' (boolean) на 'status' (integer: 1 или 0)
            'status' => $validated['isEnabled'],
            // Поле 'token' (если оно не null) можно сгенерировать здесь
             'token' => Hash::make('tonen'), 
        ]);

        // 3. Возврат Inertia-совместимого ответа (редирект)
        // После успешного создания перенаправляем пользователя на список устройств
        return redirect()
            ->route('devices.index') // Замените на фактический маршрут списка
            ->with('success', 'Устройство успешно зарегистрировано!');
    }

    public function edit(Device $device){
     $deviceData = $device->toArray();
     
        // Также убедитесь, что имена полей соответствуют ожиданиям Vue-компонента:
        // Vue ожидает: 'name', 'password', 'status', 'finalDate'

        $formattedDevice = [
            'id' => $deviceData['id'],
            'name' => $deviceData['name'], // Предположим, что 'id' в базе - это 'name' в UI
            'password' => $deviceData['password'],
            'status' => (bool) $deviceData['status'], // Переименовать для соответствия пропсам
            'finalDate' => date('Y-m-d', strtotime($deviceData['final_date'])), // Форматирование даты
            // Добавьте другие нужные поля
        ];


        return Inertia::render('EditDevice', [
            // ВАЖНО: Передавайте форматированные данные, 
            // используя имя пропса, указанное в компоненте Vue: 'device'.
            'device' => $formattedDevice, 
        ]);
    }

    public function update(Request $request ,Device $device){
         // 1. Валидация входящих данных с заданными правилами
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            // Проверяем, что дата либо null (если поле пустое), либо соответствует формату YYYY-MM-DD
            'validUntilDate' => ['nullable', 'date_format:Y-m-d'],
            'isEnabled' => ['required', 'boolean'],
        ]);

       

        // 2. Создание новой записи устройства в базе данных
        // 4. Обновление записи в базе данных
        $device->update([
            'name' => $validated['name'],
            // Хеширование пароля обязательно
            'password' => $validated['password'],
            
            // Маппинг полей формы на поля базы данных:
            // validUntilDate -> final_date
            'final_date' => $validated['validUntilDate'],
            
            // isEnabled (boolean) -> status (tinyint)
            'status' => $validated['isEnabled'],
             'token' => $device->token,
            // Дополнительное поле, если есть (например, token)
            
        ]);



        // 3. Возврат Inertia-совместимого ответа (редирект)
        return redirect()
            ->route('devices.index') // Предполагаемый маршрут для списка устройств
            ->with('success', 'Устройство ' . $device->name . ' успешно зарегистрировано!');

    }

    public function destroy(Device $device){
     $device->delete();
      return redirect()
            ->route('devices.index') // Предполагаемый маршрут для списка устройств
            ->with('success', 'Устройство  удалено');

    }
}
