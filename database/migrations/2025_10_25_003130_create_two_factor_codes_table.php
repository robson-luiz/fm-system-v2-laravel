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
        Schema::create('two_factor_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code', 6)->comment('Código de 6 dígitos');
            $table->enum('method', ['email', 'sms'])->comment('Método de envio');
            $table->string('destination')->comment('Email ou telefone de destino');
            $table->timestamp('expires_at')->comment('Data de expiração do código');
            $table->timestamp('used_at')->nullable()->comment('Data de uso do código');
            $table->integer('attempts')->default(0)->comment('Número de tentativas de uso');
            $table->string('ip_address')->nullable()->comment('IP que solicitou o código');
            $table->string('user_agent')->nullable()->comment('User agent que solicitou o código');
            $table->timestamps();

            $table->index(['user_id', 'code']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_codes');
    }
};