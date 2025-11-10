<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Http\Requests\IncomeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Income::forUser(Auth::id())->with('user');

        // Filtros
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('received_date', $request->month)
                  ->whereYear('received_date', $request->year);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        $incomes = $query->orderBy('received_date', 'desc')->paginate(12);

        // Estatísticas
        $stats = [
            'total' => Income::forUser(Auth::id())->count(),
            'received' => Income::forUser(Auth::id())->byStatus('recebida')->count(),
            'pending' => Income::forUser(Auth::id())->byStatus('pendente')->count(),
            'total_amount' => Income::forUser(Auth::id())->byStatus('recebida')->sum('amount'),
            'total_pending' => Income::forUser(Auth::id())->byStatus('pendente')->sum('amount'),
            'total_received_month' => Income::forUser(Auth::id())->currentMonth()->byStatus('recebida')->sum('amount'),
            'fixed_count' => Income::forUser(Auth::id())->byType('fixa')->count(),
        ];

        // Categorias para filtro
        $categories = Income::getDefaultCategories();

        return view('finance.incomes.index', compact('incomes', 'stats', 'categories'))
            ->with('menu', 'incomes');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Income::getDefaultCategories();
        return view('finance.incomes.create', compact('categories'))
            ->with('menu', 'incomes');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncomeRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            $income = Income::create($validatedData);

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'Receita cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao cadastrar receita: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        // Verificar se a receita pertence ao usuário autenticado
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        return view('finance.incomes.show', compact('income'))
            ->with('menu', 'incomes');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        // Verificar se a receita pertence ao usuário autenticado
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $categories = Income::getDefaultCategories();
        return view('finance.incomes.edit', compact('income', 'categories'))
            ->with('menu', 'incomes');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncomeRequest $request, Income $income)
    {
        // Verificar se a receita pertence ao usuário autenticado
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $income->update($validatedData);

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'Receita atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao atualizar receita: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        // Verificar se a receita pertence ao usuário autenticado
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        try {
            DB::beginTransaction();

            $income->delete();

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'Receita excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir receita: ' . $e->getMessage());
        }
    }

    /**
     * Toggle income status (received/pending)
     */
    public function toggleStatus(Income $income)
    {
        // Verificar se a receita pertence ao usuário autenticado
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        try {
            $newStatus = $income->status === 'recebida' ? 'pendente' : 'recebida';
            $income->update(['status' => $newStatus]);

            $message = $newStatus === 'recebida' ? 'Receita marcada como recebida!' : 'Receita marcada como pendente!';
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao alterar status: ' . $e->getMessage());
        }
    }
}
