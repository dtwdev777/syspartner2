<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\ClientLink;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Services\IpGenerator;
use App\Services\IptvPortal;

use Throwable;
class ControllerClient extends Controller
{

  
    public function index(){
      $clients = Client::all();
      return Inertia::render('Users',[
        "clients" => $clients,
        
      ]);
    }


    public function view(Request $request) // ⭐️ Используем Route Model Binding
{
   $client = Client::findOrFail($request->id);
     
$client->load('links'); // Загружаем связанные ссылки

    // Форматируем массив ссылок для Vue (просто массив URL-строк)
    $linksArray = $client->links->pluck('url')->toArray();
   
    
    // Подготовка данных для передачи в Inertia
    $clientData = [
        'id' => $client->id,
        'name' => $client->name,
        'token' => $client->token,
        'isActive' => $client->is_active, // Преобразуем имя поля в camelCase для Vue
        'limitCount' => $client->limit_count,
        'finalDate' => $client->final_date?->toDateString(), // Форматируем дату в 'YYYY-MM-DD'
        'links' => $linksArray,
    ];
  

    return Inertia::render('EditUser', [
        'user' => $clientData,
    ]);
}

   public function store(Request $request)
{
    // 1. Валидация данных
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'token' => 'required|string|unique:clients,token',
        'isActive' => 'boolean', // Получено из isActive
        'limitCount' => 'required|integer|min:0',
        'finalDate' => 'required|date', // Получено из finalDate
        'links' => 'nullable|array',
        'links.*' => 'nullable|url',
    ]);

    // Преобразование имён полей для базы данных
    $client = Client::create([
        'name' => $validated['name'],
        'token' => $validated['token'],
        'is_active' => $validated['isActive'],
        'limit_count' => $validated['limitCount'],
        'final_date' => $validated['finalDate'],
    ]);

    // 2. Сохранение ссылок (Link)
    if (!empty($validated['links'])) {
        $linksToInsert = collect($validated['links'])
            ->filter() // Удаляем пустые строки
            ->map(fn($url) => [
                'client_id' => $client->id,
                'url' => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

        ClientLink::insert($linksToInsert);
    }

    // 3. Редирект на страницу списка клиентов
    return redirect()->route('clients.form')->with('success', 'Клиент успешно создан.');
}

public function destroy(Client $client) // Используем Route Model Binding
{
    // Удаляем клиента
    $client->delete();

    // Редирект с сообщением об успехе. Inertia обновит страницу списка.
    return redirect()->route('clients.index')->with('success', 'Клиент успешно удален.');
}
/**
     * Обновляет указанного клиента и его ссылки.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Client $client)
    {
     // dd($client);
        // 1. Валидация данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Токен должен быть уникален, исключая текущего клиента
            'token' => 'required|string|unique:clients,token,' . $client->id, 
            'isActive' => 'boolean',
            'limitCount' => 'required|integer|min:0',
            'finalDate' => 'required|date',
            
            // Валидация массива ссылок
            'links' => 'nullable|array',
            'links.*' => 'nullable|url|max:2048', // Каждая ссылка должна быть URL
        ]);

        // 2. Использование транзакции для атомарности (либо всё, либо ничего)
        DB::transaction(function () use ($client, $validated) {
            
            // 2.1 Обновление основной модели Client
            $client->update([
                'name' => $validated['name'],
                'token' => $validated['token'],
                'is_active' => $validated['isActive'],
                'limit_count' => $validated['limitCount'],
                'final_date' => $validated['finalDate'],
            ]);

            // 2.2 Обновление/Замена связанных ссылок (Links)
            
            // Удаляем все старые ссылки клиента
            $client->links()->delete(); 
            
            // Подготавливаем новые ссылки для вставки
            if (!empty($validated['links'])) {
                
                // Фильтруем пустые строки и создаем массив данных для вставки
                $linksToInsert = collect($validated['links'])
                    ->filter() // Убираем пустые/null ссылки из массива
                    ->map(fn($url) => [
                        'client_id' => $client->id,
                        'url' => $url,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray();
                
                // Вставляем все новые ссылки одной операцией
                if (!empty($linksToInsert)) {
                    ClientLink::insert($linksToInsert);
                }
            }

        }); // Конец транзакции

        // 3. Редирект на форму редактирования или на список
        return redirect()->route('client.edit', $client) // Вернуть на ту же форму
                         ->with('success', 'Клиент успешно обновлен.');
        
        // ИЛИ на список:
        // return redirect()->route('clients.index')->with('success', 'Клиент успешно обновлен.');
    }


    public function client_package(Client $client){

      $packages = Package::all();

      $client->load('links'); // Загружаем связанные ссылки
     // Форматируем массив ссылок для Vue (просто массив URL-строк)
    $linksArray = $client->links->pluck('url')->toArray();
   

    // Передаём клиента и список пакетов во Vue
   return inertia('ClientPackageForm', [
        'client' => [
            'id' => $client->id,
            'name' => $client->name,
            'package_id' => $client->packages()->first()?->id, // 👈 текущий пакет
        ],
        'packages' => $packages,
        'links' => $linksArray,
    ]);
}

public function client_save(Request $request, Client $client)
{
   $validated = $request->validate([
        'package_id' => 'required|exists:packages,id',
    ]);

   

    // Если клиент может иметь только один пакет → sync
    $client->packages()->sync([$validated['package_id']]);

    return redirect()->route('clients.index')->with('success', 'Пакет обновлён для клиента');
}


public function playlist(Request $request , IpGenerator $ipGenerator){
  try{
    $hash = $request->input('token');
    $type = $request->input('type');
   
    $client = Client::where('token', $hash)->firstOrFail();

// Получаем первый пакет клиента (если нужен только один)
$package = $client->packages()->first();

// Коллекция для плейлиста
$playlist = collect();

// Формируем плейлист по странам и категориям
foreach ($package->countries as $country) {

    foreach ($country->channels as $channel) {
        $playlist->push([
             $country->name => $channel,
        ]);
    }
}



            
              if(!$playlist->isEmpty()){
                $data = $ipGenerator::create_playlist($playlist ,"", $type, $hash);
                // header('Content-Type: text/plain');
                //  header  ontent-Disposition: attachment; filename="'.$download.'"');
               
                 return response($data,200)->header('Content-Type','audio/x-mpegurl')
                  ->header('Expires', '-1')
                  ->header('Cache-Control', 'must-revalidate')
                  ->header('Pragma', 'public');

              }
              else{
                  return response()->json([ 'msg'=>'package not exit']);
              }
             
          
          }
       catch(Throwable $err){
        return response()->json(['msg'=>$err->getMessage()]);
    }
   }


  
  
      


}

    

   



