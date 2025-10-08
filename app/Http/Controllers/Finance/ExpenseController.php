<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Installment;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query base - apenas despesas do usuário logado
        $query = $user->expenses()->with(['creditCard', 'installments']);
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('periodicity')) {
            $query->where('periodicity', $request->periodicity);
        }
        
        if ($request->filled('credit_card_id')) {
            $query->where('credit_card_id', $request->credit_card_id);
        }
        
        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('due_date', $date->year)
                  ->whereMonth('due_date', $date->month);
        }
        
        // Ordenação
        $expenses = $query->orderBy('due_date', 'desc')
                          ->paginate(15)
                          ->withQueryString();
        
        // Estatísticas
        $stats = [
            'total_pending' => $user->expenses()->pending()->sum('amount'),
            'total_paid_month' => $user->expenses()->paid()->thisMonth()->sum('amount'),
            'overdue_count' => $user->expenses()->overdue()->count(),
            'due_soon_count' => $user->expenses()->dueSoon()->count(),
        ];
        
        // Cartões para filtro
        $creditCards = $user->creditCards()->active()->get();
        
        return view('finance.expenses.index', compact('expenses', 'stats', 'creditCards'))
            ->with('menu', 'expenses');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $creditCards = Auth::user()->creditCards()->active()->get();
        
        return view('finance.expenses.create', compact('creditCards'))
            ->with('menu', 'expenses');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação base
        $rules = [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'periodicity' => 'required|in:one-time,monthly,biweekly,bimonthly,semiannual,yearly',
            'credit_card_id' => 'nullable|exists:credit_cards,id',
            'num_installments' => 'nullable|integer|min:1|max:60',
            'installment_type' => 'nullable|in:equal,custom',
        ];

        // Se for parcelas personalizadas, validar array de parcelas
        if ($request->input('installment_type') === 'custom') {
            $rules['custom_installments'] = 'required|array';
            $rules['custom_installments.*.amount'] = 'required|numeric|min:0.01';
            $rules['custom_installments.*.due_date'] = 'required|date';
        }

        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            $validated['user_id'] = Auth::id();
            $validated['status'] = 'pending';
            
            // Verificar se o cartão pertence ao usuário
            if ($validated['credit_card_id']) {
                $creditCard = CreditCard::where('id', $validated['credit_card_id'])
                    ->where('user_id', Auth::id())
                    ->first();
                    
                if (!$creditCard) {
                    return back()->with('error', 'Cartão de crédito inválido.')->withInput();
                }
            }
            
            // Se tem parcelas, criar despesa principal e parcelas
            if (isset($validated['num_installments']) && $validated['num_installments'] > 1) {
                // Verificar se é parcelamento personalizado
                if ($request->input('installment_type') === 'custom' && $request->has('custom_installments')) {
                    $this->createExpenseWithCustomInstallments($validated, $request->input('custom_installments'));
                } else {
                    $this->createExpenseWithInstallments($validated);
                }
            } else {
                // Criar despesa única
                Expense::create($validated);
            }
            
            DB::commit();
            
            return redirect()
                ->route('expenses.index')
                ->with('success', 'Despesa cadastrada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Erro ao cadastrar despesa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $expense->load(['creditCard', 'installments']);
        
        return view('finance.expenses.show', compact('expense'))
            ->with('menu', 'expenses');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $creditCards = Auth::user()->creditCards()->active()->get();
        
        return view('finance.expenses.edit', compact('expense', 'creditCards'))
            ->with('menu', 'expenses');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'periodicity' => 'required|in:one-time,monthly,biweekly,bimonthly,semiannual,yearly',
            'credit_card_id' => 'nullable|exists:credit_cards,id',
            'status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'reason_not_paid' => 'nullable|string',
        ]);
        
        // Verificar se o cartão pertence ao usuário
        if ($validated['credit_card_id']) {
            $creditCard = CreditCard::where('id', $validated['credit_card_id'])
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$creditCard) {
                return back()->with('error', 'Cartão de crédito inválido.')->withInput();
            }
        }
        
        $expense->update($validated);
        
        return redirect()
            ->route('expenses.show', $expense)
            ->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        DB::beginTransaction();
        
        try {
            // Se for despesa principal com parcelas, deletar todas
            if ($expense->hasInstallments()) {
                $expense->installments()->delete();
            }
            
            $expense->delete();
            
            DB::commit();
            
            return redirect()
                ->route('expenses.index')
                ->with('success', 'Despesa excluída com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Erro ao excluir despesa: ' . $e->getMessage());
        }
    }

    /**
     * Marcar despesa como paga
     */
    public function markAsPaid(Request $request, Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);
        
        $expense->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
            'reason_not_paid' => null,
        ]);
        
        return back()->with('success', 'Despesa marcada como paga!');
    }

    /**
     * Marcar despesa como não paga
     */
    public function markAsUnpaid(Request $request, Expense $expense)
    {
        // Verificar se a despesa pertence ao usuário
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $validated = $request->validate([
            'reason_not_paid' => 'required|string|max:500',
        ]);
        
        $expense->update([
            'status' => 'pending',
            'payment_date' => null,
            'reason_not_paid' => $validated['reason_not_paid'],
        ]);
        
        return back()->with('success', 'Motivo registrado com sucesso!');
    }

    /**
     * Criar despesa com parcelas (valores iguais)
     */
    private function createExpenseWithInstallments(array $data)
    {
        // Criar despesa principal
        $expense = Expense::create([
            'user_id' => $data['user_id'],
            'credit_card_id' => $data['credit_card_id'] ?? null,
            'description' => $data['description'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
            'periodicity' => $data['periodicity'],
            'status' => 'pending',
            'num_installments' => $data['num_installments'],
        ]);
        
        // Criar parcelas na tabela installments
        $installmentAmount = round($data['amount'] / $data['num_installments'], 2);
        $totalDistributed = 0;
        
        for ($i = 1; $i <= $data['num_installments']; $i++) {
            // Calcular valor da parcela (última parcela ajusta diferença de arredondamento)
            if ($i == $data['num_installments']) {
                $amount = $data['amount'] - $totalDistributed;
            } else {
                $amount = $installmentAmount;
                $totalDistributed += $amount;
            }
            
            // Calcular data de vencimento
            $dueDate = Carbon::parse($data['due_date'])->addMonths($i - 1);
            
            Installment::create([
                'expense_id' => $expense->id,
                'installment_number' => $i,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }
        
        return $expense;
    }

    /**
     * Criar despesa com parcelas personalizadas (valores variados)
     */
    private function createExpenseWithCustomInstallments(array $data, array $customInstallments)
    {
        // Calcular valor total das parcelas customizadas
        $totalCustom = array_sum(array_column($customInstallments, 'amount'));
        
        // Criar despesa principal
        $expense = Expense::create([
            'user_id' => $data['user_id'],
            'credit_card_id' => $data['credit_card_id'] ?? null,
            'description' => $data['description'],
            'amount' => $totalCustom, // Usar soma das parcelas como total
            'due_date' => $customInstallments[0]['due_date'] ?? $data['due_date'],
            'periodicity' => $data['periodicity'],
            'status' => 'pending',
            'num_installments' => count($customInstallments),
        ]);
        
        // Criar parcelas personalizadas
        foreach ($customInstallments as $index => $installmentData) {
            Installment::create([
                'expense_id' => $expense->id,
                'installment_number' => $index + 1,
                'amount' => $installmentData['amount'],
                'due_date' => $installmentData['due_date'],
                'status' => 'pending',
            ]);
        }
        
        return $expense;
    }

    /**
     * Marcar parcela como paga
     */
    public function markInstallmentAsPaid(Request $request, Installment $installment)
    {
        // Verificar se a parcela pertence a uma despesa do usuário
        if ($installment->expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }
        
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);
        
        $installment->markAsPaid($validated['payment_date']);
        
        return response()->json([
            'success' => true,
            'message' => 'Parcela marcada como paga!',
            'installment' => $installment->fresh(),
        ]);
    }

    /**
     * Marcar parcela como não paga
     */
    public function markInstallmentAsUnpaid(Request $request, Installment $installment)
    {
        // Verificar se a parcela pertence a uma despesa do usuário
        if ($installment->expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }
        
        $validated = $request->validate([
            'reason_not_paid' => 'nullable|string|max:500',
        ]);
        
        $installment->markAsUnpaid($validated['reason_not_paid'] ?? null);
        
        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
            'installment' => $installment->fresh(),
        ]);
    }
}
