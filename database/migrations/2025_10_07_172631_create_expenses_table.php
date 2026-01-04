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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relacionamento com cartão de crédito (opcional)
            $table->foreignId('credit_card_id')->nullable()->constrained()->onDelete('set null');
            
            // Informações básicas
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            
            // Periodicidade
            $table->enum('periodicity', [
                'one-time',
                'monthly',
                'biweekly',
                'bimonthly',
                'semiannual',
                'yearly'
            ])->default('one-time');
            
            // Status de pagamento
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            
            // Parcelas
            $table->integer('num_installments')->default(1);
            $table->boolean('is_installment_parent')->default(false); // Se é a despesa principal que gera parcelas
            $table->foreignId('parent_expense_id')->nullable()->constrained('expenses')->onDelete('cascade'); // Referência à despesa principal
            $table->integer('installment_number')->nullable(); // Número da parcela (1, 2, 3...)
            
            // Motivo de não pagamento
            $table->text('reason_not_paid')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id', 'status', 'due_date']);
            $table->index(['user_id', 'periodicity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
