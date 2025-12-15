<?php

namespace App\Http\Controllers;

use App\Services\CashFlowService;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    protected $cashFlowService;
    
    public function __construct(CashFlowService $cashFlowService)
    {
        $this->cashFlowService = $cashFlowService;
    }
    
    /**
     * Display cash flow analysis page
     */
    public function index()
    {
        return view('cash-flow.index');
    }
    
    /**
     * Get complete cash flow data for charts
     */
    public function getData(Request $request)
    {
        $userId = auth()->id();
        $months = $request->input('months', 12);
        
        $data = $this->cashFlowService->getCompleteAnalysis($userId, $months);
        
        return response()->json($data);
    }
    
    /**
     * Get monthly flow data
     */
    public function getMonthlyFlow(Request $request)
    {
        $userId = auth()->id();
        $months = $request->input('months', 12);
        
        $data = $this->cashFlowService->getMonthlyFlow($userId, $months);
        
        return response()->json($data);
    }
    
    /**
     * Get projections data
     */
    public function getProjections(Request $request)
    {
        $userId = auth()->id();
        $futureMonths = $request->input('future_months', 6);
        
        $data = $this->cashFlowService->getProjections($userId, $futureMonths);
        
        return response()->json($data);
    }
    
    /**
     * Get yearly summary
     */
    public function getYearlySummary(Request $request)
    {
        $userId = auth()->id();
        $year = $request->input('year', date('Y'));
        
        $data = $this->cashFlowService->getYearlyFlow($userId, $year);
        
        return response()->json($data);
    }
}
