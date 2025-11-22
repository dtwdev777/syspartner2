<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\IptvPortal;
use App\Services\StreamParserService;
use App\Services\IpGenerator;

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
         $this->app->singleton(StreamParserService::class, function ($app) {
            // Здесь вы можете передать любые конфигурационные данные,
            // например, настройки из config/services.php
            
           // Если ваш класс принимает аргументы, передайте их здесь
            return new StreamParserService(); 
        });

         $this->app->singleton(IpGenerator::class, function ($app) {
            // Здесь вы можете передать любые конфигурационные данные,
            // например, настройки из config/services.php
            
           // Если ваш класс принимает аргументы, передайте их здесь
            return new IpGenerator(); 
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
