<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\IptvPortal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->singleton(IptvPortal::class, function ($app) {
            // Здесь вы можете передать любые конфигурационные данные,
            // например, настройки из config/services.php
            
           // Если ваш класс принимает аргументы, передайте их здесь
            return new IptvPortal(); 
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
