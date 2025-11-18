<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeView;
use App\Http\Controllers\ControllerClient;
use App\Http\Controllers\DeviceController;

Route::get('/home', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::get('/check',[HomeView::class,'check']);

Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/',[ControllerClient::class , 'index'])->name('clients.index');;

Route::get('/form', function(){
  return Inertia::render('AddUser');
})->name('clients.form');


Route::get('/edit-form/{id}',[ControllerClient::class ,'view'])->name('client.edit');
Route::delete('/client/{client}',[ControllerClient::class ,'destroy']);
Route::put('/client/{client}', [ControllerClient::class, 'update'])->name('clients.update');
Route::post('/client/save', [ControllerClient::class ,'store']);

});

Route::get('/devices',[DeviceController::class,'index'])->name('devices.index');
Route::get('/device-edit/{device}',[DeviceController::class,'edit'])->name('device.edit');
Route::get('/device-form',[DeviceController::class,'create'])->name('device.form');
Route::post('/device-create',[DeviceController::class,'store'])->name('device.create');

Route::put('/device-update/{device}',[DeviceController::class,'update'])->name('device.update');
Route::delete('/device-delete/{device}',[DeviceController::class,'destroy'])->name('device.destroy');

Route::get('/admin', function(){
  return Inertia::render('LoginForm');
})->name('login');
Route::get('dashboard', [ControllerClient::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
