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
        // Создаем связующую таблицу channel_country.
        Schema::create('channel_country', function (Blueprint $table) {
            // Главный идентификатор записи в этой таблице.
            $table->id(); 
            
            // Внешний ключ, ссылающийся на таблицу 'channels'.
            // Используем foreignId() для краткости и соблюдения соглашений Laravel.
            $table->foreignId('channel_id')
                  ->constrained() // Добавляем внешний ключ, ссылающийся на channels.id
                  ->onDelete('cascade'); // Если канал удаляется, удаляются и все его связи.
            
            // Внешний ключ, ссылающийся на таблицу 'countries'.
            $table->foreignId('country_id')
                  ->constrained() // Добавляем внешний ключ, ссылающийся на countries.id
                  ->onDelete('cascade'); // Если страна удаляется, удаляются и все связанные записи.
            
            // Добавляем уникальный индекс для пары channel_id и country_id.
            // Это гарантирует, что один канал не будет дважды связан с одной и той же страной.
            $table->unique(['channel_id', 'country_id']);

            // Внешние ключи уже проиндексированы по умолчанию с помощью constrained().
            
            // В данной таблице timestamps обычно не нужны, но можно добавить по желанию:
            // $table->timestamps();
        });
    }

    /**
     * Откат миграции (удаление таблицы).
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_country');
    }

};
