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
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // 'id'
            $table->string('name'); // 'name'
            $table->string('token')->unique(); // 'token' (уникальный)
            $table->boolean('is_active')->default(true); // 'isActive'
            $table->unsignedInteger('limit_count')->default(0); // 'limitCount' (целое, без знака)
            $table->timestamp('final_date')->nullable(); // 'finalDate' (дата/время, может быть NULL)
            
            // Внимание: поле 'links' будет храниться в отдельной таблице client_links
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
