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
        Schema::create('channels', function (Blueprint $table) {
            // Главный идентификатор (Primary Key).
            $table->id(); 
            
            // Краткое уникальное имя или слаг канала (например, 'bbc_news').
            $table->string('name', 100)->unique(); 
            
            // Полное отображаемое название канала (например, 'BBC News - World').
            $table->string('title', 255); 
            
            // Ссылка/URL канала.
            $table->string('link', 255); 
            
            // Метки времени 'created_at' и 'updated_at'.
            $table->timestamps();
        });
    }

    /**
     * Откат миграции (удаление таблицы).
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
