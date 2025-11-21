<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;
use App\Models\Package;

class Packages extends Controller
{
 
  public function index()
    {
        // Загружаем все пакеты с привязанными странами
        $packages = Package::with('countries')->get();

        // Возвращаем во Vue/Inertia
        return inertia('Packages', [
            'packages' => $packages,
        ]);
    }
  
    public function create(Request $request){
         $countries = Country::all(['id','name']);
     
         return Inertia::render('CreatePackage', [
        'countries' =>  $countries,
    ]);
    }

    public function edit(Package $package)
{
    // Загружаем все страны и текущие связи
    $countries = Country::all();

    // Передаём пакет и список стран во Vue
    return inertia('EditPackage', [
        'package' => [
            'id' => $package->id,
            'name' => $package->name,
            'is_active' => $package->is_active,
            'countries' => $package->countries()->pluck('countries.id'),

        ],
        'countries' => $countries,
    ]);
}



public function update(Request $request, Package $package)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'is_active' => 'boolean',
        'countries' => 'nullable|array',
        'countries.*' => 'exists:countries,id',
    ]);

    // Обновляем пакет
    $package->update([
        'name' => $validated['name'],
        'is_active' => $validated['is_active'] ?? false,
    ]);

    // Синхронизируем страны
    $package->countries()->sync($validated['countries'] ?? []);

    return redirect()->route('packages.index')->with('success', 'Пакет обновлён');
}


    

    public function store(Request $request)
{
    // Валидация входных данных
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'countries' => 'nullable|array',
        'countries.*' => 'exists:countries,id',
        'is_active' => 'boolean',
    ]);

    //dd($validated);

   
    $package = Package::create([
        'name' => $validated['name'],
        'is_active' => $validated['is_active'] ?? false,
    ]);

    // Привязка стран
    if (!empty($validated['countries'])) {
        $package->countries()->attach($validated['countries']);
    }

    return redirect()->route('packages.index')->with('success', 'Канал создан');
}
public function destroy(Package $package)
{
    // Удаляем связи с странами
    $package->countries()->detach();

    // Удаляем сам пакет
    $package->delete();

    return redirect()->route('packages.index')->with('success', 'Пакет удалён');
}
}
