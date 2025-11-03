<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Query base - apenas cartões do usuário logado
        // Nota: O método creditCards() está definido no modelo User (linha 78-81)
        $query = $user->creditCards()->withCount('expenses');
        
        // Filtros
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('bank')) {
            $query->where('bank', 'like', '%' . $request->bank . '%');
        }
        
        // Ordenação
        $creditCards = $query->orderBy('name', 'asc')
                            ->paginate(15)
                            ->withQueryString();
        
        // Estatísticas
        $stats = [
            'total' => $user->creditCards()->count(),
            'active' => $user->creditCards()->where('is_active', true)->count(),
            'inactive' => $user->creditCards()->where('is_active', false)->count(),
            'total_limit' => $user->creditCards()->where('is_active', true)->sum('card_limit'),
            'total_available' => $user->creditCards()->where('is_active', true)->sum('available_limit'),
        ];
        
        // Adicionar dados calculados para cada cartão
        foreach ($creditCards as $card) {
            $card->used_limit = $card->card_limit - $card->available_limit;
            $card->usage_percentage = $card->card_limit > 0 ? round(($card->used_limit / $card->card_limit) * 100, 1) : 0;
            
            // Próximo vencimento
            $today = Carbon::today();
            $currentMonth = $today->month;
            $currentYear = $today->year;
            
            // Se já passou o dia de vencimento deste mês, calcular para o próximo mês
            if ($today->day > $card->due_day) {
                $nextDue = Carbon::create($currentYear, $currentMonth, $card->due_day)->addMonth();
            } else {
                $nextDue = Carbon::create($currentYear, $currentMonth, $card->due_day);
            }
            
            $card->next_due_date = $nextDue;
            $card->days_to_due = $today->diffInDays($nextDue, false);
            
            // Melhor dia para compra (se não definido, calcular automaticamente)
            if (!$card->best_purchase_day) {
                // Melhor dia é logo após o vencimento (mais tempo para pagar)
                $card->calculated_best_day = $card->due_day + 1;
                if ($card->calculated_best_day > 31) {
                    $card->calculated_best_day = 1;
                }
            }
        }
        
        return view('finance.credit_cards.index', compact('creditCards', 'stats'))
            ->with('menu', 'credit-cards');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.credit_cards.create')
            ->with('menu', 'credit-cards');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'last_four_digits' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_limit' => 'required|numeric|min:0|max:999999.99',
            'closing_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'best_purchase_day' => 'nullable|integer|min:1|max:31',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'annual_fee' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'O nome do cartão é obrigatório.',
            'name.max' => 'O nome do cartão não pode ter mais de 255 caracteres.',
            'bank.required' => 'O banco emissor é obrigatório.',
            'bank.max' => 'O banco emissor não pode ter mais de 255 caracteres.',
            'last_four_digits.size' => 'Os últimos 4 dígitos devem ter exatamente 4 números.',
            'last_four_digits.regex' => 'Os últimos 4 dígitos devem conter apenas números.',
            'card_limit.required' => 'O limite do cartão é obrigatório.',
            'card_limit.numeric' => 'O limite deve ser um valor numérico.',
            'card_limit.min' => 'O limite deve ser maior que zero.',
            'card_limit.max' => 'O limite não pode ser maior que R$ 999.999,99.',
            'closing_day.required' => 'O dia de fechamento é obrigatório.',
            'closing_day.min' => 'O dia de fechamento deve ser entre 1 e 31.',
            'closing_day.max' => 'O dia de fechamento deve ser entre 1 e 31.',
            'due_day.required' => 'O dia de vencimento é obrigatório.',
            'due_day.min' => 'O dia de vencimento deve ser entre 1 e 31.',
            'due_day.max' => 'O dia de vencimento deve ser entre 1 e 31.',
            'best_purchase_day.min' => 'O melhor dia para compra deve ser entre 1 e 31.',
            'best_purchase_day.max' => 'O melhor dia para compra deve ser entre 1 e 31.',
            'interest_rate.numeric' => 'A taxa de juros deve ser um valor numérico.',
            'interest_rate.min' => 'A taxa de juros deve ser maior ou igual a zero.',
            'interest_rate.max' => 'A taxa de juros não pode ser maior que 100%.',
            'annual_fee.numeric' => 'A anuidade deve ser um valor numérico.',
            'annual_fee.min' => 'A anuidade deve ser maior ou igual a zero.',
            'annual_fee.max' => 'A anuidade não pode ser maior que R$ 999.999,99.',
        ]);
        
        try {
            DB::beginTransaction();
            
            $validated['user_id'] = Auth::id();
            $validated['available_limit'] = $validated['card_limit']; // Inicialmente, limite disponível = limite total
            $validated['is_active'] = $request->input('is_active', '0') === '1';
            
            // Se não informou o melhor dia para compra, calcular automaticamente
            if (!$validated['best_purchase_day']) {
                $validated['best_purchase_day'] = $validated['due_day'] + 1;
                if ($validated['best_purchase_day'] > 31) {
                    $validated['best_purchase_day'] = 1;
                }
            }
            
            $creditCard = CreditCard::create($validated);
            
            DB::commit();
            
            return redirect()->route('credit-cards.index')
                           ->with('success', 'Cartão de crédito cadastrado com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar cartão de crédito. Tente novamente.')
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditCard $creditCard)
    {
        // Verificar se o cartão pertence ao usuário
        if ($creditCard->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        // Carregar relacionamentos
        $creditCard->load(['expenses.installments']);
        
        // Calcular dados adicionais
        $creditCard->used_limit = $creditCard->card_limit - $creditCard->available_limit;
        $creditCard->usage_percentage = $creditCard->card_limit > 0 ? 
            round(($creditCard->used_limit / $creditCard->card_limit) * 100, 1) : 0;
        
        // Próximo vencimento
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;
        
        if ($today->day > $creditCard->due_day) {
            $nextDue = Carbon::create($currentYear, $currentMonth, $creditCard->due_day)->addMonth();
        } else {
            $nextDue = Carbon::create($currentYear, $currentMonth, $creditCard->due_day);
        }
        
        $creditCard->next_due_date = $nextDue;
        $creditCard->days_to_due = $today->diffInDays($nextDue, false);
        
        // Despesas do cartão (últimas 10)
        $recentExpenses = $creditCard->expenses()
                                   ->with('installments')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10)
                                   ->get();
        
        // Estatísticas do cartão
        $stats = [
            'total_expenses' => $creditCard->expenses()->count(),
            'pending_expenses' => $creditCard->expenses()->where('status', 'pending')->count(),
            'paid_expenses' => $creditCard->expenses()->where('status', 'paid')->count(),
            'total_amount' => $creditCard->expenses()->sum('amount'),
            'pending_amount' => $creditCard->expenses()->where('status', 'pending')->sum('amount'),
        ];
        
        return view('finance.credit_cards.show', compact('creditCard', 'recentExpenses', 'stats'))
            ->with('menu', 'credit-cards');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditCard $creditCard)
    {
        // Verificar se o cartão pertence ao usuário
        if ($creditCard->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        return view('finance.credit_cards.edit', compact('creditCard'))
            ->with('menu', 'credit-cards');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditCard $creditCard)
    {
        // Verificar se o cartão pertence ao usuário
        if ($creditCard->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'last_four_digits' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_limit' => 'required|numeric|min:0|max:999999.99',
            'available_limit' => 'required|numeric|min:0|max:999999.99',
            'closing_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'best_purchase_day' => 'nullable|integer|min:1|max:31',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'annual_fee' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean',
            'auto_calculate_limit' => 'boolean',
        ], [
            'name.required' => 'O nome do cartão é obrigatório.',
            'name.max' => 'O nome do cartão não pode ter mais de 255 caracteres.',
            'bank.required' => 'O banco emissor é obrigatório.',
            'bank.max' => 'O banco emissor não pode ter mais de 255 caracteres.',
            'last_four_digits.size' => 'Os últimos 4 dígitos devem ter exatamente 4 números.',
            'last_four_digits.regex' => 'Os últimos 4 dígitos devem conter apenas números.',
            'card_limit.required' => 'O limite do cartão é obrigatório.',
            'card_limit.numeric' => 'O limite deve ser um valor numérico.',
            'card_limit.min' => 'O limite deve ser maior que zero.',
            'card_limit.max' => 'O limite não pode ser maior que R$ 999.999,99.',
            'available_limit.required' => 'O limite disponível é obrigatório.',
            'available_limit.numeric' => 'O limite disponível deve ser um valor numérico.',
            'available_limit.min' => 'O limite disponível deve ser maior ou igual a zero.',
            'available_limit.max' => 'O limite disponível não pode ser maior que R$ 999.999,99.',
            'closing_day.required' => 'O dia de fechamento é obrigatório.',
            'closing_day.min' => 'O dia de fechamento deve ser entre 1 e 31.',
            'closing_day.max' => 'O dia de fechamento deve ser entre 1 e 31.',
            'due_day.required' => 'O dia de vencimento é obrigatório.',
            'due_day.min' => 'O dia de vencimento deve ser entre 1 e 31.',
            'due_day.max' => 'O dia de vencimento deve ser entre 1 e 31.',
            'best_purchase_day.min' => 'O melhor dia para compra deve ser entre 1 e 31.',
            'best_purchase_day.max' => 'O melhor dia para compra deve ser entre 1 e 31.',
            'interest_rate.numeric' => 'A taxa de juros deve ser um valor numérico.',
            'interest_rate.min' => 'A taxa de juros deve ser maior ou igual a zero.',
            'interest_rate.max' => 'A taxa de juros não pode ser maior que 100%.',
            'annual_fee.numeric' => 'A anuidade deve ser um valor numérico.',
            'annual_fee.min' => 'A anuidade deve ser maior ou igual a zero.',
            'annual_fee.max' => 'A anuidade não pode ser maior que R$ 999.999,99.',
        ]);
        
        // Validação customizada: limite disponível não pode ser maior que o limite total
        if ($validated['available_limit'] > $validated['card_limit']) {
            return back()->with('error', 'O limite disponível não pode ser maior que o limite total do cartão.')
                        ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $validated['is_active'] = $request->input('is_active', '0') === '1';
            $validated['auto_calculate_limit'] = $request->input('auto_calculate_limit', '0') === '1';
            
            // Se não informou o melhor dia para compra, calcular automaticamente
            if (!$validated['best_purchase_day']) {
                $validated['best_purchase_day'] = $validated['due_day'] + 1;
                if ($validated['best_purchase_day'] > 31) {
                    $validated['best_purchase_day'] = 1;
                }
            }
            
            // Se o cálculo automático estiver habilitado, recalcular o limite disponível
            if ($validated['auto_calculate_limit']) {
                $totalUsed = $creditCard->expenses()
                    ->where('status', 'pending')
                    ->sum('amount');
                $validated['available_limit'] = max(0, $validated['card_limit'] - $totalUsed);
            }
            
            $creditCard->update($validated);
            
            DB::commit();
            
            return redirect()->route('credit-cards.show', $creditCard)
                           ->with('success', 'Cartão de crédito atualizado com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar cartão de crédito. Tente novamente.')
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCard $creditCard)
    {
        // Verificar se o cartão pertence ao usuário
        if ($creditCard->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        // Verificar se há despesas vinculadas ao cartão
        $expensesCount = $creditCard->expenses()->count();
        
        if ($expensesCount > 0) {
            return back()->with('error', "Não é possível excluir o cartão pois há {$expensesCount} despesa(s) vinculada(s) a ele.");
        }
        
        try {
            DB::beginTransaction();
            
            $creditCard->delete();
            
            DB::commit();
            
            return redirect()->route('credit-cards.index')
                           ->with('success', 'Cartão de crédito excluído com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir cartão de crédito. Tente novamente.');
        }
    }

    /**
     * Toggle card status (active/inactive)
     */
    public function toggleStatus(CreditCard $creditCard)
    {
        // Verificar se o cartão pertence ao usuário
        if ($creditCard->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        try {
            DB::beginTransaction();
            
            $creditCard->update([
                'is_active' => !$creditCard->is_active
            ]);
            
            DB::commit();
            
            $status = $creditCard->is_active ? 'ativado' : 'desativado';
            
            return back()->with('success', "Cartão {$status} com sucesso!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao alterar status do cartão. Tente novamente.');
        }
    }

    /**
     * Calcular limite usado automaticamente baseado nas despesas vinculadas ao cartão
     */
    private function calculateUsedLimit(CreditCard $creditCard)
    {
        // Buscar todas as despesas não pagas vinculadas a este cartão
        // Assumindo que existe um relacionamento com a tabela expenses
        // Se não existir ainda, podemos implementar depois
        
        // Por enquanto, vamos retornar o valor atual para não quebrar
        return $creditCard->used_limit ?? 0;
    }

    /**
     * Atualizar limite disponível automaticamente
     */
    private function updateAvailableLimit(CreditCard $creditCard)
    {
        $usedLimit = $this->calculateUsedLimit($creditCard);
        $availableLimit = $creditCard->card_limit - $usedLimit;
        
        $creditCard->update([
            'used_limit' => $usedLimit,
            'available_limit' => max(0, $availableLimit) // Não permitir valor negativo
        ]);
        
        return $creditCard;
    }
}