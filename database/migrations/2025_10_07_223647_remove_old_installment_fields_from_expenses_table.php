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
        Schema::table('expenses', function (Blueprint $table) {
            // Remove campos antigos do sistema de parcelas
            $table->dropForeign(['parent_expense_id']);
            $table->dropColumn([
                'is_installment_parent',
                'parent_expense_id',
                'installment_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Restaura os campos caso necessário fazer rollback
            $table->boolean('is_installment_parent')->default(false);
            $table->foreignId('parent_expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
            $table->integer('installment_number')->nullable();
        });
    }
};
