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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('received_date');
            $table->string('category');
            $table->enum('type', ['fixa', 'variavel'])->default('variavel');
            $table->enum('status', ['recebida', 'pendente'])->default('pendente');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['user_id', 'received_date']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
