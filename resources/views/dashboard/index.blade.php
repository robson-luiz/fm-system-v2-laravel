@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Dashboard</h2>
            <nav class="breadcrumb">
                <span>Dashboard</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <x-alert />
        
        <!-- Cards de Estatísticas Principais -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            
            <!-- Card Receitas do Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Receitas do Mês</p>
                        <div class="flex items-baseline">
                            <p class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white truncate" data-stat="incomes-monthly-received">
                                R$ {{ number_format($incomesStats['monthly_received'], 2, ',', '.') }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                            Pendente: <span data-stat="incomes-monthly-pending">R$ {{ number_format($incomesStats['monthly_pending'], 2, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="p-2 md:p-3 bg-green-100 dark:bg-green-900 rounded-full flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4">
                    <div class="flex items-center text-xs md:text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium">
                            {{ $incomesStats['fixed_count'] }} fixas
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 mx-1 md:mx-2">•</span>
                        <span class="text-blue-600 dark:text-blue-400 truncate">
                            {{ $incomesStats['upcoming_count'] }} próximas
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Despesas do Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Despesas do Mês</p>
                        <div class="flex items-baseline">
                            <p class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white truncate" data-stat="expenses-monthly-paid">
                                R$ {{ number_format($expensesStats['monthly_paid'], 2, ',', '.') }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                            Pendente: <span data-stat="expenses-monthly-pending">R$ {{ number_format($expensesStats['monthly_pending'], 2, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="p-2 md:p-3 bg-red-100 dark:bg-red-900 rounded-full flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4">
                    @if($expensesStats['overdue_count'] > 0)
                        <div class="flex items-center text-xs md:text-sm">
                            <span class="text-red-600 dark:text-red-400 font-medium">
                                <span data-stat="expenses-overdue-count">{{ $expensesStats['overdue_count'] }}</span> vencidas
                            </span>
                            @if($expensesStats['due_soon_count'] > 0)
                                <span class="text-gray-500 dark:text-gray-400 mx-1 md:mx-2">•</span>
                                <span class="text-yellow-600 dark:text-yellow-400 truncate">
                                    <span data-stat="expenses-due-soon-count">{{ $expensesStats['due_soon_count'] }}</span> próximas
                                </span>
                            @endif
                        </div>
                    @elseif($expensesStats['due_soon_count'] > 0)
                        <div class="flex items-center text-xs md:text-sm">
                            <span class="text-yellow-600 dark:text-yellow-400">
                                <span data-stat="expenses-due-soon-count">{{ $expensesStats['due_soon_count'] }}</span> vencendo em breve
                            </span>
                        </div>
                    @else
                        <div class="flex items-center text-xs md:text-sm">
                            <span class="text-green-600 dark:text-green-400">
                                Todas em dia
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Saldo do Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Saldo do Mês</p>
                        <div class="flex items-baseline">
                            <p class="text-xl md:text-2xl font-semibold {{ $balance['monthly_actual'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} truncate" data-stat="balance-monthly-actual">
                                R$ {{ number_format(abs($balance['monthly_actual']), 2, ',', '.') }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                            Projetado: R$ {{ number_format($balance['monthly_projected'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-2 md:p-3 {{ $balance['monthly_actual'] >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full flex-shrink-0">
                        @if($balance['monthly_actual'] >= 0)
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="mt-3 md:mt-4">
                    <div class="flex items-center text-xs md:text-sm">
                        <span class="{{ $balance['monthly_actual'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                            {{ $balance['monthly_actual'] >= 0 ? 'Superávit' : 'Déficit' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Cartões de Crédito -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Cartões de Crédito</p>
                        <div class="flex items-baseline">
                            <p class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $creditCardsStats['total_count'] }}
                            </p>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 ml-2">ativos</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                            Limite: R$ {{ number_format($creditCardsStats['total_limit'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-2 md:p-3 bg-blue-100 dark:bg-blue-900 rounded-full flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4">
                    <div class="flex items-center text-xs md:text-sm">
                        @if($creditCardsStats['cards_near_limit'] > 0)
                            <span class="text-red-600 dark:text-red-400 font-medium truncate">
                                {{ $creditCardsStats['cards_near_limit'] }} próximo(s) ao limite
                            </span>
                        @else
                            <span class="text-green-600 dark:text-green-400">
                                Uso: {{ number_format($creditCardsStats['average_usage'], 1) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas Inteligentes -->
        @if($alertsCounts['high'] > 0 || $alertsCounts['medium'] > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-r from-red-100 to-orange-200 dark:from-red-800 dark:to-orange-800 rounded-lg shadow-sm p-6 text-gray-800 dark:text-white mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold">Central de Alertas</h3>
                        <p class="text-gray-600 dark:text-gray-200 mt-1">
                            {{ $alertsCounts['high'] }} alertas críticos, {{ $alertsCounts['medium'] }} importantes
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $alertsCounts['high'] + $alertsCounts['medium'] }}</div>
                        <div class="text-gray-600 dark:text-gray-200 text-sm">alertas ativos</div>
                    </div>
                </div>
            </div>

            <!-- Alertas Importantes (Alta Prioridade) -->
            @if(count($alertsSummary) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($alertsSummary as $alert)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 
                        @if($alert['type'] === 'danger') border-red-500 
                        @elseif($alert['type'] === 'warning') border-yellow-500
                        @elseif($alert['type'] === 'info') border-blue-500
                        @else border-green-500 @endif
                        p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($alert['type'] === 'danger')
                                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                @elseif($alert['type'] === 'warning')
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($alert['type'] === 'info')
                                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $alert['title'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $alert['message'] }}</p>
                                @if(isset($alert['suggested_action']))
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 italic">
                                        <strong>Sugestão:</strong> {{ $alert['suggested_action'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Alertas Detalhados -->
        @if($alerts['overdue_expenses']->count() > 0 || $alerts['due_soon_expenses']->count() > 0 || $alerts['overdue_incomes']->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Alertas de Despesas -->
            @if($alerts['overdue_expenses']->count() > 0 || $alerts['due_soon_expenses']->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <svg class="h-5 w-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Despesas Pendentes</h3>
                </div>

                @if($alerts['overdue_expenses']->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Vencidas</h4>
                        <div class="space-y-2">
                            @foreach($alerts['overdue_expenses']->take(3) as $expenseData)
                                @php $expense = $expenseData['expense'] @endphp
                                <div class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $expense->description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Venceu há {{ $expenseData['days_overdue'] }} dia(s) - {{ $expense->due_date_formatted }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        {{ $expense->amount_formatted }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($alerts['due_soon_expenses']->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-yellow-600 dark:text-yellow-400 mb-2">Vencendo em Breve</h4>
                        <div class="space-y-2">
                            @foreach($alerts['due_soon_expenses']->take(3) as $expenseData)
                                @php $expense = $expenseData['expense'] @endphp
                                <div class="flex justify-between items-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $expense->description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Vence em {{ $expenseData['days_until_due'] }} dia(s) - {{ $expense->due_date_formatted }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">
                                        {{ $expense->amount_formatted }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Outros Alertas -->
            @if($alerts['overdue_incomes']->count() > 0 || count($alerts['credit_card_alerts']) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Outras Notificações</h3>
                </div>

                @if($alerts['overdue_incomes']->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-2">Receitas em Atraso</h4>
                        <div class="space-y-2">
                            @foreach($alerts['overdue_incomes']->take(3) as $incomeData)
                                @php $income = $incomeData['income'] @endphp
                                <div class="flex justify-between items-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $income->description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $income->category }} - Atrasada há {{ $incomeData['days_overdue'] }} dia(s)
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $income->formatted_amount }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($alerts['credit_card_alerts']) > 0)
                    <div>
                        <h4 class="text-sm font-medium text-purple-600 dark:text-purple-400 mb-2">Alertas de Cartão</h4>
                        <div class="space-y-2">
                            @foreach(array_slice($alerts['credit_card_alerts'], 0, 3) as $alert)
                                @if(isset($alert['card']))
                                    @php $card = $alert['card'] @endphp
                                    <div class="flex justify-between items-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $card->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $alert['message'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                                {{ number_format($card->limit_usage_percentage, 1) }}%
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Área dos Gráficos -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
            
            <!-- Gráfico Receitas vs Despesas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Receitas vs Despesas</h3>
                    <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Últimos 6 meses</span>
                </div>
                <div class="h-64 md:h-80">
                    <canvas id="incomeVsExpenseChart"></canvas>
                </div>
            </div>

            <!-- Gráfico Uso dos Cartões -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Uso dos Cartões</h3>
                    <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Atual</span>
                </div>
                <div class="h-64 md:h-80">
                    @if($creditCardsStats['total_count'] > 0)
                        <canvas id="creditCardUsageChart"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <svg class="h-8 w-8 md:h-12 md:w-12 mx-auto mb-2 md:mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <p class="text-sm">Nenhum cartão de crédito cadastrado</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Links Rápidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Ações Rápidas</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                @can('create-expense')
                    <a href="{{ route('expenses.create') }}" class="flex flex-col items-center p-3 md:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-red-600 dark:text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        <span class="text-xs md:text-sm font-medium text-gray-900 dark:text-white text-center">Nova Despesa</span>
                    </a>
                @endcan

                @can('create-income')
                    <a href="{{ route('incomes.create') }}" class="flex flex-col items-center p-3 md:p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-xs md:text-sm font-medium text-gray-900 dark:text-white text-center">Nova Receita</span>
                    </a>
                @endcan

                @can('create-credit-card')
                    <a href="{{ route('credit-cards.create') }}" class="flex flex-col items-center p-3 md:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="text-xs md:text-sm font-medium text-gray-900 dark:text-white text-center">Novo Cartão</span>
                    </a>
                @endcan

                <a href="{{ route('expenses.index') }}" class="flex flex-col items-center p-3 md:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                    <svg class="h-6 w-6 md:h-8 md:w-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-xs md:text-sm font-medium text-gray-900 dark:text-white text-center">Relatórios</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Dados para os gráficos Chart.js -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script type="application/json" id="chartData">
        @json($chartsData)
    </script>
    @endpush
    
@endsection
