<?php

namespace App\Http\Controllers;

use App\Services\TrendAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrendReportController extends Controller
{
    protected $trendService;

    public function __construct(TrendAnalysisService $trendService)
    {
        $this->trendService = $trendService;
    }

    /**
     * Exibe a página de relatórios de tendências
     */
    public function index()
    {
        $userId = Auth::id();
        $months = 12; // Padrão: 12 meses
        
        $summary = $this->trendService->getSummary($userId, $months);
        $trends = $this->trendService->calculateTrends($userId, $months);
        
        return view('trends.index', [
            'summary' => $summary,
            'trends' => $trends,
            'months' => $months,
            'menu' => 'trends', // Para marcar o menu lateral como ativo
        ]);
    }

    /**
     * Retorna dados históricos por categoria (AJAX)
     */
    public function getHistoricalData(Request $request)
    {
        $userId = Auth::id();
        $months = $request->input('months', 12);
        
        $data = $this->trendService->getHistoricalDataByCategory($userId, $months);
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Retorna cálculos de tendências (AJAX)
     */
    public function getTrends(Request $request)
    {
        $userId = Auth::id();
        $months = $request->input('months', 12);
        
        $trends = $this->trendService->calculateTrends($userId, $months);
        $summary = $this->trendService->getSummary($userId, $months);
        
        return response()->json([
            'success' => true,
            'trends' => $trends,
            'summary' => $summary,
        ]);
    }

    /**
     * Retorna projeções futuras (AJAX)
     */
    public function getProjections(Request $request)
    {
        $userId = Auth::id();
        $historicalMonths = $request->input('historical_months', 6);
        $futureMonths = $request->input('future_months', 6);
        
        $projections = $this->trendService->generateProjections($userId, $historicalMonths, $futureMonths);
        
        return response()->json([
            'success' => true,
            'projections' => $projections,
        ]);
    }

    /**
     * Retorna padrões sazonais (AJAX)
     */
    public function getSeasonalPatterns(Request $request)
    {
        $userId = Auth::id();
        $months = $request->input('months', 12);
        
        $patterns = $this->trendService->detectSeasonalPatterns($userId, $months);
        
        return response()->json([
            'success' => true,
            'patterns' => $patterns,
        ]);
    }
}
