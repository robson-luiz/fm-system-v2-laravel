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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informações do cartão
            $table->string('name'); // Nome/apelido do cartão (ex: "Cartão Nubank", "Visa Gold")
            $table->string('bank'); // Banco emissor
            $table->string('last_four_digits', 4)->nullable(); // Últimos 4 dígitos
            
            // Limites
            $table->decimal('card_limit', 10, 2); // Limite total do cartão
            $table->decimal('available_limit', 10, 2); // Limite disponível
            
            // Datas importantes
            $table->integer('closing_day')->comment('Dia do fechamento da fatura (1-31)');
            $table->integer('due_day')->comment('Dia do vencimento da fatura (1-31)');
            $table->integer('best_purchase_day')->nullable()->comment('Melhor dia para compras (1-31)');
            
            // Taxas
            $table->decimal('interest_rate', 5, 2)->default(0)->comment('Taxa de juros ao mês (%)');
            $table->decimal('annual_fee', 10, 2)->default(0)->comment('Anuidade');
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
