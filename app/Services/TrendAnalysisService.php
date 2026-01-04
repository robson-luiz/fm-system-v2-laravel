<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendAnalysisService
{
    /**
     * Obtém dados históricos de gastos por categoria
     *
     * @param int $userId
     * @param int $months Número de meses para análise (6, 12, 24)
     * @return array
     */
    public function getHistoricalDataByCategory(int $userId, int $months = 12): array
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $categories = Category::active()->orderedByName()->get();
        
        $result = [];
        
        foreach ($categories as $category) {
            $monthlyData = [];
            
            // Pegar dados de cada mês
            for ($i = $months - 1; $i >= 0; $i--) {
                $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
                $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
                
                // Buscar despesas do mês para essa categoria
                $total = Expense::where('user_id', $userId)
                    ->where('category_id', $category->id)
                    ->whereBetween('due_date', [$monthStart, $monthEnd])
                    ->sum('amount');
                
                $monthlyData[] = [
                    'month' => $monthStart->format('M/Y'),
                    'month_full' => $monthStart->format('F Y'),
                    'year_month' => $monthStart->format('Y-m'),
                    'amount' => floatval($total),
                ];
            }
            
            $result[] = [
                'category' => $category->name,
                'icon' => $category->icon,
                'color' => $category->color,
                'data' => $monthlyData,
            ];
        }
        
        return $result;
    }

    /**
     * Calcula tendências de crescimento/queda por categoria
     *
     * @param int $userId
     * @param int $months
     * @return array
     */
    public function calculateTrends(int $userId, int $months = 12): array
    {
        $historicalData = $this->getHistoricalDataByCategory($userId, $months);
        
        $trends = [];
        
        foreach ($historicalData as $categoryData) {
            $monthlyAmounts = array_column($categoryData['data'], 'amount');
            
            // Calcular total gasto nessa categoria
            $total = array_sum($monthlyAmounts);
            
            // Calcular média mensal
            $average = $total > 0 ? $total / count($monthlyAmounts) : 0;
            
            // Pegar primeiros 3 meses vs últimos 3 meses
            $firstPeriod = array_slice($monthlyAmounts, 0, 3);
            $lastPeriod = array_slice($monthlyAmounts, -3);
            
            $avgFirst = array_sum($firstPeriod) / count($firstPeriod);
            $avgLast = array_sum($lastPeriod) / count($lastPeriod);
            
            // Calcular variação percentual
            $percentChange = 0;
            if ($avgFirst > 0) {
                $percentChange = (($avgLast - $avgFirst) / $avgFirst) * 100;
            }
            
            // Determinar tendência
            $trend = 'stable';
            if ($percentChange > 10) {
                $trend = 'up';
            } elseif ($percentChange < -10) {
                $trend = 'down';
            }
            
            $trends[] = [
                'category' => $categoryData['category'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'total' => $total,
                'average' => $average,
                'percent_change' => round($percentChange, 2),
                'trend' => $trend,
                'first_period_avg' => $avgFirst,
                'last_period_avg' => $avgLast,
            ];
        }
        
        // Ordenar por total (maior para menor)
        usort($trends, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        return $trends;
    }

    /**
     * Gera projeções futuras baseadas em média móvel
     *
     * @param int $userId
     * @param int $historicalMonths Meses históricos para base
     * @param int $futureMonths Meses futuros a projetar
     * @return array
     */
    public function generateProjections(int $userId, int $historicalMonths = 6, int $futureMonths = 6): array
    {
        $historicalData = $this->getHistoricalDataByCategory($userId, $historicalMonths);
        
        $projections = [];
        
        foreach ($historicalData as $categoryData) {
            $monthlyAmounts = array_column($categoryData['data'], 'amount');
            
            // Calcular média móvel dos últimos meses
            $movingAverage = array_sum($monthlyAmounts) / count($monthlyAmounts);
            
            // Calcular desvio padrão para adicionar variação realista
            $variance = 0;
            foreach ($monthlyAmounts as $amount) {
                $variance += pow($amount - $movingAverage, 2);
            }
            $stdDeviation = sqrt($variance / count($monthlyAmounts));
            
            // Gerar projeções
            $futureData = [];
            for ($i = 1; $i <= $futureMonths; $i++) {
                $futureMonth = Carbon::now()->addMonths($i);
                
                // Adicionar pequena variação (±10% do desvio padrão)
                $variation = ($stdDeviation * 0.1) * (mt_rand(-100, 100) / 100);
                $projectedAmount = max(0, $movingAverage + $variation);
                
                $futureData[] = [
                    'month' => $futureMonth->format('M/Y'),
                    'month_full' => $futureMonth->format('F Y'),
                    'year_month' => $futureMonth->format('Y-m'),
                    'amount' => round($projectedAmount, 2),
                ];
            }
            
            $projections[] = [
                'category' => $categoryData['category'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'average' => round($movingAverage, 2),
                'projections' => $futureData,
            ];
        }
        
        return $projections;
    }

    /**
     * Obtém resumo geral de tendências
     *
     * @param int $userId
     * @param int $months
     * @return array
     */
    public function getSummary(int $userId, int $months = 12): array
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        // Total gasto no período
        $totalSpent = Expense::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Média mensal
        $monthlyAverage = $totalSpent / $months;
        
        // Comparar primeira metade vs segunda metade do período
        $halfMonths = $months / 2;
        $middleDate = Carbon::now()->subMonths($halfMonths)->endOfMonth();
        
        $firstHalfTotal = Expense::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $middleDate])
            ->sum('amount');
        
        $secondHalfTotal = Expense::where('user_id', $userId)
            ->whereBetween('due_date', [$middleDate->copy()->addDay(), $endDate])
            ->sum('amount');
        
        $firstHalfAvg = $firstHalfTotal / $halfMonths;
        $secondHalfAvg = $secondHalfTotal / $halfMonths;
        
        // Calcular tendência geral
        $overallChange = 0;
        if ($firstHalfAvg > 0) {
            $overallChange = (($secondHalfAvg - $firstHalfAvg) / $firstHalfAvg) * 100;
        }
        
        $overallTrend = 'stable';
        if ($overallChange > 5) {
            $overallTrend = 'up';
        } elseif ($overallChange < -5) {
            $overallTrend = 'down';
        }
        
        // Categoria com maior crescimento
        $trends = $this->calculateTrends($userId, $months);
        $topGrowth = null;
        $topDecrease = null;
        
        foreach ($trends as $trend) {
            if ($trend['total'] > 0) { // Apenas categorias com gastos
                if ($topGrowth === null || $trend['percent_change'] > $topGrowth['percent_change']) {
                    $topGrowth = $trend;
                }
                if ($topDecrease === null || $trend['percent_change'] < $topDecrease['percent_change']) {
                    $topDecrease = $trend;
                }
            }
        }
        
        return [
            'total_spent' => $totalSpent,
            'monthly_average' => $monthlyAverage,
            'first_half_avg' => $firstHalfAvg,
            'second_half_avg' => $secondHalfAvg,
            'overall_change' => round($overallChange, 2),
            'overall_trend' => $overallTrend,
            'top_growth' => $topGrowth,
            'top_decrease' => $topDecrease,
            'period_months' => $months,
        ];
    }

    /**
     * Identifica padrões sazonais
     *
     * @param int $userId
     * @param int $months
     * @return array
     */
    public function detectSeasonalPatterns(int $userId, int $months = 12): array
    {
        $historicalData = $this->getHistoricalDataByCategory($userId, $months);
        
        $patterns = [];
        
        foreach ($historicalData as $categoryData) {
            if (array_sum(array_column($categoryData['data'], 'amount')) == 0) {
                continue; // Pular categorias sem gastos
            }
            
            // Identificar mês com maior e menor gasto
            $maxMonth = null;
            $minMonth = null;
            $maxAmount = 0;
            $minAmount = PHP_FLOAT_MAX;
            
            foreach ($categoryData['data'] as $monthData) {
                if ($monthData['amount'] > $maxAmount) {
                    $maxAmount = $monthData['amount'];
                    $maxMonth = $monthData['month_full'];
                }
                if ($monthData['amount'] < $minAmount && $monthData['amount'] > 0) {
                    $minAmount = $monthData['amount'];
                    $minMonth = $monthData['month_full'];
                }
            }
            
            $patterns[] = [
                'category' => $categoryData['category'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'peak_month' => $maxMonth,
                'peak_amount' => $maxAmount,
                'lowest_month' => $minMonth,
                'lowest_amount' => $minAmount,
            ];
        }
        
        return $patterns;
    }
}
