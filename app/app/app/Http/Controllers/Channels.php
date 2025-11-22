<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Services\StreamParserService;
use App\Models\Country;
use App\Models\Channel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;




class Channels extends Controller
{
     private $stream;
    public function __construct(StreamParserService $stream)
    {
      $this->stream = $stream;
    }

    public function index(){
       $channels = Channel::select('id', 'name', 'title', 'link')->get();
       return Inertia::render('Channels', [
        'channels' => $channels,
       ]);

    }
    public function create(Request $request){
 
     
    
       
         return Inertia::render('ChannelsCreate', [
        'countries' => [],
    ]);
    }

    public function store(Request $request){
      
      // 1. Валидация входящих данных
        $validator = Validator::make($request->all(), [
           
            'link' => ['required', 'url', 'max:255'],
            // 'countries' должен быть массивом, и все элементы должны быть числовыми ID, 
            // существующими в таблице 'countries'.
            // 'countries' => ['required', 'array', 'min:1'],
            // 'countries.*' => ['required', 'integer', 'exists:countries,id'], 
            'file' => ['required', 'file', 'max:10240'], // Макс. 10MB
        ]);

        if ($validator->fails()) {
            // Если валидация не пройдена, возвращаем ошибки обратно в Inertia
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();
        $filePath = null;

        try {
            // 2. Сохранение файла
            // 'public' - это имя диска (настроено в config/filesystems.php).
            // Файл будет сохранен в storage/app/public/resources.
            $filePath = $request->file('file')->store('resources', 'public');
            $parserData = $this->stream->parseFile($request->file('file'));
           // dd($parserData);
            // 3. Сохранение данных в базу данных в рамках транзакции
            DB::beginTransaction();
            foreach ($parserData as $val){

            
            // Создание новой записи ресурса
            $resource = Channel::create([
                'name' => $val['id'],
                'link' => $validatedData['link'],
                'title' => $val['title'], // Сохраняем путь к файлу
                // 'user_id' => auth()->id(), // Если ресурс привязан к пользователю
            ]);

            // 4. Синхронизация стран (отношение Many-to-Many)
            // Предполагается, что в модели Resource есть метод BelongsToMany 'countries()'
           /// $resource->countries()->attach($validatedData['countries']);
            }
            DB::commit();

            // 5. Успешный редирект
            return redirect()->route('channels.index')
                ->with('success', 'Ресурс "' . $resource->name . '" успешно загружен и сохранен.');

        } catch (\Exception $e) {
            // В случае ошибки откатываем транзакцию
            DB::rollBack();

            // Если файл был загружен до сбоя базы данных, удаляем его
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            // Выводим ошибку в лог и отправляем пользователю
            \Log::error('Resource upload failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Произошла ошибка при сохранении ресурса.')
                ->withInput();
        }
    }

public function edit(Channel $channel){
    
      
       return Inertia::render('EditChannel', [
        'channel' => $channel,
       ]);

}


    public function update(Request $request, Channel $channel)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'title' => 'required|string',
        'link' => 'required|url',
        
       
    ]);

    $channel->update($validated);

    return redirect()->route('channels.index')->with('success', 'Канал  обновлен');;
}
 public function destroy(Channel $channel){
     $channel->delete();
     
      return redirect()
            ->route('channels.index') // Предполагаемый маршрут для списка устройств
            ->with('success', 'Канал  удален');

    }

    public function country_add(){
        $countries = Country::all(['id','name']);
        $channels = Channel::all(['id' ,'name']);
         $countries_list = Channel::with('countries')->get();
        
        return Inertia::render('CountryChannels', [
        'channels' => $channels,
        'countries' => $countries,
        'countries_list' =>  $countries_list
       ]);

    }
    
    public function showByCountry($countryId)
{
    $channels = Channel::whereHas('countries', function ($query) use ($countryId) {
        $query->where('countries.id', $countryId);
    })->get();


    return Inertia::render('ChannelsByCountry', [
        'channels' => $channels,
        'country' => Country::find($countryId),
    ]);

    
}

    public function country_save(Request $request)
{
 
    $validated = $request->validate([
        'channels' => 'required|array',
        'countries' => 'required|array',
    ]);

    foreach ($validated['channels'] as $channelId) {
        foreach ($validated['countries'] as $countryId) {
            DB::table('channel_country')->updateOrInsert([
                'channel_id' => $channelId,
                'country_id' => $countryId,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Привязка выполнена');
}

public function detach(Channel $channel, Country $country)
{
    $channel->countries()->detach($country->id);

    return redirect()->back()->with('success', 'Страна отвязана от канала');
}

public function all_delete(Request $request)
{
    $ids = $request->input('ids');

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['error' => 'Нет ID для удаления'], 400);
    }

    Channel::whereIn('id', $ids)->delete();

    return redirect()
            ->route('channels.index') // Предполагаемый маршрут для списка устройств
            ->with('success', 'Каналы  удален');
}

}



