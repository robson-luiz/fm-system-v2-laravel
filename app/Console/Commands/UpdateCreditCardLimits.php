<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CreditCard;

class UpdateCreditCardLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credit-cards:update-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualizar limites disponíveis dos cartões de crédito baseado nas despesas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Atualizando limites dos cartões de crédito...');
        
        $creditCards = CreditCard::where('auto_calculate_limit', true)->get();
        
        $updated = 0;
        
        foreach ($creditCards as $creditCard) {
            // Calcular o total usado baseado em todas as despesas pendentes do cartão
            $totalUsed = $creditCard->expenses()
                ->where('status', 'pending')
                ->sum('amount');

            // Calcular limite disponível
            $availableLimit = max(0, $creditCard->card_limit - $totalUsed);

            // Atualizar se houver diferença
            if ($creditCard->available_limit != $availableLimit) {
                $creditCard->updateQuietly([
                    'available_limit' => $availableLimit
                ]);
                $updated++;
                
                $this->line("Cartão '{$creditCard->name}': R$ " . number_format($availableLimit, 2, ',', '.'));
            }
        }
        
        $this->info("✅ Atualização concluída! {$updated} cartões atualizados.");
        
        return 0;
    }
}
