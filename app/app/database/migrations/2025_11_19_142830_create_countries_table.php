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
      // Создаем таблицу 'countries' для хранения списка стран мира.
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            // Название страны (например, "Russian Federation").
            $table->string('name', 100); 
            // ISO 3166-1 Alpha-2 код страны (например, "RU"). Должен быть уникальным.
            $table->char('iso_code', 2)->unique(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('countries');
    }
};
