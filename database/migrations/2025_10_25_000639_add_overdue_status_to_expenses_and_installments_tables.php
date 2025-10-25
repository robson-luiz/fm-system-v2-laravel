<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se é MySQL ou SQLite
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Usar ALTER TABLE MODIFY para ENUMs
            DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('pending', 'paid', 'overdue') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid', 'overdue') NOT NULL DEFAULT 'pending'");
            
            // Atualizar registros vencidos usando CURDATE()
            DB::statement("
                UPDATE expenses 
                SET status = 'overdue' 
                WHERE status = 'pending' 
                AND due_date < CURDATE()
            ");
            
            DB::statement("
                UPDATE installments 
                SET status = 'overdue' 
                WHERE status = 'pending' 
                AND due_date < CURDATE()
            ");
        } else {
            // SQLite: Não precisa alterar estrutura, apenas atualizar registros
            // SQLite trata ENUMs como strings, então 'overdue' já é válido
            
            // Atualizar registros vencidos usando DATE('now')
            DB::statement("
                UPDATE expenses 
                SET status = 'overdue' 
                WHERE status = 'pending' 
                AND due_date < DATE('now')
            ");
            
            DB::statement("
                UPDATE installments 
                SET status = 'overdue' 
                WHERE status = 'pending' 
                AND due_date < DATE('now')
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter registros 'overdue' para 'pending' (funciona em ambos)
        DB::statement("UPDATE expenses SET status = 'pending' WHERE status = 'overdue'");
        DB::statement("UPDATE installments SET status = 'pending' WHERE status = 'overdue'");
        
        // Verificar se é MySQL para reverter ENUMs
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Reverter enum para o estado anterior
            DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending'");
        }
        // SQLite: Não precisa reverter estrutura
    }
};