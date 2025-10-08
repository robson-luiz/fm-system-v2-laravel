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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com despesa
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            
            // Número da parcela
            $table->integer('installment_number')->comment('Número da parcela (1, 2, 3...)');
            
            // Valor da parcela
            $table->decimal('amount', 10, 2);
            
            // Data de vencimento
            $table->date('due_date');
            
            // Status da parcela
            $table->enum('status', ['pending', 'paid'])->default('pending');
            
            // Data de pagamento
            $table->date('payment_date')->nullable();
            
            // Motivo de não pagamento
            $table->text('reason_not_paid')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['expense_id', 'status']);
            $table->index(['expense_id', 'installment_number']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
