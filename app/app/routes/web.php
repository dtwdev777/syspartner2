<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeView;
use App\Http\Controllers\ControllerClient;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Packages;
use App\Http\Controllers\Channels;

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

Route::get('/devices',[DeviceController::class,'index'])->name('devices.index');
Route::get('/device-edit/{device}',[DeviceController::class,'edit'])->name('device.edit');
Route::get('/device-form',[DeviceController::class,'create'])->name('device.form');
Route::post('/device-create',[DeviceController::class,'store'])->name('device.create');

Route::put('/device-update/{device}',[DeviceController::class,'update'])->name('device.update');
Route::delete('/device-delete/{device}',[DeviceController::class,'destroy'])->name('device.destroy');

//chanels
Route::get('/channels',[Channels::class ,'index'])->name('channels.index');

Route::get('/channel/{channel}', [Channels::class, 'edit'])->name('channel.edit');
Route::put('/channel/{channel}/update', [Channels::class, 'update'])->name('channel.update');

Route::get('/channel-new',[Channels::class ,'create'])->name('channel.new');
Route::post('/channel-create',[Channels::class ,'store'])->name('channel.store');
Route::delete('/channel/{channel}', [Channels::class, 'destroy'])->name('channel.delete');

Route::get('/countries',[Channels::class ,'country_add'])->name('countries.index');
Route::post('/countries',[Channels::class ,'country_save'])->name('countries.store');
Route::delete('/channel-country/{channel}/{country}', [Channels::class, 'detach']);
//Packages
Route::get('/packages',[Packages::class ,'index'])->name('packages.index');
Route::get('/package-new',[Packages::class ,'create'])->name('package.new');
Route::get('/package-edit/{package}',[Packages::class ,'edit'])->name('package.edit');
Route::put('/package/{package}/update',[Packages::class ,'update'])->name('package.update');
Route::post('/package',[Packages::class ,'store'])->name('package.store');
Route::delete('/package/{package}',[Packages::class ,'destroy'])->name('package.delete');

});




Route::get('/admin', function(){
  return Inertia::render('LoginForm');
})->name('login');
Route::get('dashboard', [ControllerClient::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
