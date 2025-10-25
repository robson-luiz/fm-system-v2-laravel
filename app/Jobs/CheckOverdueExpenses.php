<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckOverdueExpenses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $today = now()->toDateString();
                
                // Atualizar despesas vencidas (pending + due_date < hoje)
                $overdueExpenses = Expense::where('status', 'pending')
                    ->where('due_date', '<', $today)
                    ->update([
                        'status' => 'overdue',
                        'updated_at' => now()
                    ]);

                // Atualizar parcelas vencidas (pending + due_date < hoje)
                $overdueInstallments = Installment::where('status', 'pending')
                    ->where('due_date', '<', $today)
                    ->update([
                        'status' => 'overdue',
                        'updated_at' => now()
                    ]);

                Log::info('Job CheckOverdueExpenses executado com sucesso', [
                    'overdue_expenses' => $overdueExpenses,
                    'overdue_installments' => $overdueInstallments,
                    'executed_at' => now()
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Erro ao executar job CheckOverdueExpenses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw para que o job seja marcado como falhou
            throw $e;
        }
    }
}