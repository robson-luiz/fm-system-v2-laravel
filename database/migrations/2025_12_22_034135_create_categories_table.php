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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da categoria
            $table->string('slug')->unique(); // Slug único para URLs
            $table->string('icon')->nullable(); // Ícone (emoji ou classe CSS)
            $table->string('color', 7)->default('#6B7280'); // Cor hex (#RRGGBB)
            $table->boolean('is_active')->default(true); // Ativa/Inativa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
