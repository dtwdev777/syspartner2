<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_links', function (Blueprint $table) {
            $table->id();
            
            // Внешний ключ, связывающий ссылку с клиентом
            $table->foreignId('client_id')
                  ->constrained() // Создает внешний ключ на таблицу clients (id)
                  ->onDelete('cascade'); // Если клиент удаляется, удаляются и его ссылки
                  
            $table->string('url', 2048); // Поле для хранения самой ссылки ('links')
            
            $table->timestamps();
            
            // Дополнительно: можно добавить индекс для быстрого поиска по client_id
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_links');
    }
};