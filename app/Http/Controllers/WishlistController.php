<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Wishlist;
use App\Services\WishlistViabilityService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected $viabilityService;

    public function __construct(WishlistViabilityService $viabilityService)
    {
        $this->viabilityService = $viabilityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = Wishlist::forUser($userId)
            ->byStatus($request->status)
            ->byPriority($request->priority)
            ->orderBy('priority', 'desc')
            ->orderBy('target_date', 'asc');
        
        $wishlists = $query->paginate(12);
        
        // Estatísticas
        $stats = [
            'total' => Wishlist::forUser($userId)->count(),
            'em_progresso' => Wishlist::forUser($userId)->where('status', 'em_progresso')->count(),
            'concluidas' => Wishlist::forUser($userId)->where('status', 'concluida')->count(),
            'total_valor' => Wishlist::forUser($userId)->where('status', 'em_progresso')->sum('target_amount'),
            'total_economizado' => Wishlist::forUser($userId)->where('status', 'em_progresso')->sum('current_amount'),
        ];
        
        return view('wishlist.index', compact('wishlists', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Wishlist::getStatuses();
        $priorities = Wishlist::getPriorities();
        
        return view('wishlist.create', compact('statuses', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['current_amount'] = $data['current_amount'] ?? 0;
            $data['status'] = $data['status'] ?? 'em_progresso';
            
            Wishlist::create($data);
            
            return redirect()->route('wishlist.index')
                ->with('success', '🎯 Objetivo adicionado à wishlist com sucesso!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao adicionar objetivo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        // Análise de viabilidade
        $analysis = $this->viabilityService->analyzeViability($wishlist, auth()->id());
        
        return view('wishlist.show', compact('wishlist', 'analysis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $statuses = Wishlist::getStatuses();
        $priorities = Wishlist::getPriorities();
        
        return view('wishlist.edit', compact('wishlist', 'statuses', 'priorities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WishlistRequest $request, Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        try {
            $data = $request->validated();
            
            // Verificar se completou o objetivo
            if ($data['current_amount'] >= $wishlist->target_amount && $wishlist->status !== 'concluida') {
                $data['status'] = 'concluida';
            }
            
            $wishlist->update($data);
            
            return redirect()->route('wishlist.show', $wishlist)
                ->with('success', '✅ Objetivo atualizado com sucesso!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar objetivo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        try {
            $wishlist->delete();
            
            return redirect()->route('wishlist.index')
                ->with('success', '🗑️ Objetivo removido da wishlist.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao remover objetivo: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar valor ao progresso
     */
    public function addProgress(Request $request, Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }
        
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);
            
            $newAmount = $wishlist->current_amount + $request->amount;
            
            // Não permitir ultrapassar o valor alvo
            if ($newAmount > $wishlist->target_amount) {
                $newAmount = $wishlist->target_amount;
            }
            
            $wishlist->current_amount = $newAmount;
            
            // Marcar como concluída se atingiu o valor
            if ($newAmount >= $wishlist->target_amount) {
                $wishlist->status = 'concluida';
            }
            
            $wishlist->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Progresso atualizado!',
                'current_amount' => $wishlist->current_amount,
                'progress_percentage' => $wishlist->progress_percentage,
                'status' => $wishlist->status,
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obter análise de viabilidade via AJAX
     */
    public function getViabilityAnalysis(Wishlist $wishlist)
    {
        // Verificar se o wishlist pertence ao usuário
        if ($wishlist->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }
        
        try {
            $analysis = $this->viabilityService->analyzeViability($wishlist, auth()->id());
            
            return response()->json($analysis);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

