@extends('layouts.admin')

@section('title', 'Relatórios de Tendências')

@section('content')
<!-- Título e Trilha de Navegação -->
<div class="content-wrapper">
    <div class="content-header">
        <h2 class="content-title">📊 Relatórios de Tendências</h2>
        <nav class="breadcrumb">
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
            <span>/</span>
            <span>Tendências</span>
        </nav>
    </div>
</div>

<div class="content-box">
    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- Total Gasto -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Gasto</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white truncate" id="stat-total">
                        R$ {{ number_format($summary['total_spent'], 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Últimos <span id="period-label">{{ $summary['period_months'] }}</span> meses</p>
                </div>
                <div class="p-2 md:p-3 bg-blue-100 dark:bg-blue-900 rounded-full flex-shrink-0">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>

        <!-- Média Mensal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Média Mensal</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white truncate" id="stat-average">
                        R$ {{ number_format($summary['monthly_average'], 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Por mês no período</p>
                </div>
                <div class="p-2 md:p-3 bg-purple-100 dark:bg-purple-900 rounded-full flex-shrink-0">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>

        <!-- Tendência Geral -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Tendência Geral</p>
                    <p class="text-xl md:text-2xl font-semibold truncate" id="stat-trend">
                        <span class="{{ $summary['overall_change'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $summary['overall_change'] > 0 ? '+' : '' }}{{ number_format($summary['overall_change'], 2) }}%
                        </span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                        {{ $summary['overall_trend'] === 'up' ? 'Crescimento' : ($summary['overall_trend'] === 'down' ? 'Redução' : 'Estável') }}
                    </p>
                </div>
                <div class="p-2 md:p-3 bg-orange-100 dark:bg-orange-900 rounded-full flex-shrink-0">
                    @if($summary['overall_trend'] === 'up')
                        <span class="text-2xl">📈</span>
                    @elseif($summary['overall_trend'] === 'down')
                        <span class="text-2xl">📉</span>
                    @else
                        <span class="text-2xl">➡️</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Categoria Destaque -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Maior Variação</p>
                    @if($summary['top_growth'] && abs($summary['top_growth']['percent_change']) > abs($summary['top_decrease']['percent_change'] ?? 0))
                        <p class="text-lg font-semibold text-gray-900 dark:text-white truncate" id="stat-highlight">
                            {{ $summary['top_growth']['icon'] }} {{ $summary['top_growth']['category'] }}
                        </p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            +{{ number_format($summary['top_growth']['percent_change'], 2) }}% de aumento
                        </p>
                    @elseif($summary['top_decrease'])
                        <p class="text-lg font-semibold text-gray-900 dark:text-white truncate" id="stat-highlight">
                            {{ $summary['top_decrease']['icon'] }} {{ $summary['top_decrease']['category'] }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            {{ number_format($summary['top_decrease']['percent_change'], 2) }}% de redução
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sem dados suficientes</p>
                    @endif
                </div>
                <div class="p-2 md:p-3 bg-green-100 dark:bg-green-900 rounded-full flex-shrink-0">
                    <span class="text-2xl">🎯</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="mb-6 flex justify-end gap-2">
        <button 
            onclick="changePeriod(6)" 
            id="btn-6" 
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600">
            6 meses
        </button>
        <button 
            onclick="changePeriod(12)" 
            id="btn-12" 
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white">
            12 meses
        </button>
        <button 
            onclick="changePeriod(24)" 
            id="btn-24" 
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600">
            24 meses
        </button>
    </div>

    <!-- Gráfico de Evolução por Categoria -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6 mb-6 md:mb-8">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-4">📈 Evolução por Categoria</h2>
        <div class="h-64 md:h-96">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Tabela de Tendências -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 md:mb-8">
        <div class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-4">📋 Análise Detalhada por Categoria</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Categoria
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Gasto
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Média Mensal
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tendência
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Variação
                        </th>
                    </tr>
                </thead>
                <tbody id="trends-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($trends as $trend)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">{{ $trend['icon'] }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $trend['category'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($trend['total'], 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-400">
                            R$ {{ number_format($trend['average'], 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($trend['trend'] === 'up')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                    📈 Crescimento
                                </span>
                            @elseif($trend['trend'] === 'down')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                    📉 Redução
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400">
                                    ➡️ Estável
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold">
                            <span class="{{ $trend['percent_change'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $trend['percent_change'] > 0 ? '+' : '' }}{{ number_format($trend['percent_change'], 2) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gráfico de Projeções -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-2">🔮 Projeções Futuras (Próximos 6 Meses)</h2>
        <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 mb-4">
            Baseado em média móvel dos últimos 6 meses
        </p>
        <div class="h-64 md:h-96">
            <canvas id="projectionChart"></canvas>
        </div>
    </div>
</div>
@endsection
