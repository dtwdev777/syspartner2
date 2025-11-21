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
        Schema::create('clients_packages', function (Blueprint $table) {
            $table->id();

            // Внешние ключи
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');

            // Дополнительные поля (если нужно)
          
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            // Уникальность пары client-package
            $table->unique(['client_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients_packages');
    }
};
