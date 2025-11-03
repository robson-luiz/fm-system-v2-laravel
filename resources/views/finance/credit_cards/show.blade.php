@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('credit-cards.index') }}" class="breadcrumb-link">Cartões de Crédito</a>
                <span>/</span>
                <span>{{ $creditCard->name }}</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">{{ $creditCard->name }}</h3>
            <div class="content-box-btn flex space-x-2">
                @can('edit-credit-card')
                    <a href="{{ route('credit-cards.edit', $creditCard) }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone pencil (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Editar</span>
                    </a>
                @endcan
                
                <a href="{{ route('credit-cards.index') }}" class="btn-secondary-md align-icon-btn">
                    <!-- Ícone arrow-left (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Voltar</span>
                </a>
            </div>
        </div>

        <x-alert />

        <!-- Card Principal do Cartão -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-white mb-6 shadow-lg">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold">{{ $creditCard->name }}</h2>
                    <p class="text-blue-100 text-lg">{{ $creditCard->bank }}</p>
                    @if($creditCard->last_four_digits)
                        <p class="text-blue-100 text-sm mt-2">•••• •••• •••• {{ $creditCard->last_four_digits }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if($creditCard->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ● Ativo
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ● Inativo
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informações do Limite -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-blue-100 text-sm">Limite Total</p>
                    <p class="text-2xl font-bold">{{ $creditCard->card_limit_formatted }}</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm">Limite Disponível</p>
                    <p class="text-2xl font-bold">{{ $creditCard->available_limit_formatted }}</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm">Uso do Limite</p>
                    <p class="text-2xl font-bold">{{ $creditCard->usage_percentage }}%</p>
                </div>
            </div>

            <!-- Barra de Progresso do Limite -->
            <div class="mt-4">
                <div class="w-full bg-blue-400/30 rounded-full h-3">
                    <div class="bg-white h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $creditCard->usage_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-blue-100 mt-1">
                    <span>Usado: R$ {{ number_format($creditCard->used_limit, 2, ',', '.') }}</span>
                    <span>Disponível: {{ $creditCard->available_limit_formatted }}</span>
                </div>
            </div>
        </div>

        <!-- Informações Detalhadas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Datas Importantes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Datas Importantes</h4>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Fechamento da Fatura:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">Dia {{ $creditCard->closing_day }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Vencimento da Fatura:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">Dia {{ $creditCard->due_day }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Melhor Dia para Compra:</span>
                        <span class="font-medium text-green-600">Dia {{ $creditCard->best_purchase_day }}</span>
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-blue-700 dark:text-blue-300 font-medium">Próximo Vencimento:</span>
                            <span class="text-blue-800 dark:text-blue-200 font-bold">
                                {{ $creditCard->next_due_date->format('d/m/Y') }}
                                @if($creditCard->days_to_due <= 7)
                                    <span class="text-red-600 ml-2">({{ $creditCard->days_to_due }} dias)</span>
                                @else
                                    <span class="text-green-600 ml-2">({{ $creditCard->days_to_due }} dias)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Financeiras -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Financeiras</h4>
                
                <div class="space-y-4">
                    @if($creditCard->interest_rate && $creditCard->interest_rate > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Taxa de Juros (mensal):</span>
                            <span class="font-medium text-red-600">{{ number_format($creditCard->interest_rate, 2, ',', '.') }}%</span>
                        </div>
                    @endif
                    
                    @if($creditCard->annual_fee && $creditCard->annual_fee > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Anuidade:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $creditCard->annual_fee_formatted }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Data de Cadastro:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $creditCard->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Última Atualização:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $creditCard->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Uso -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 mb-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Estatísticas de Uso</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_expenses'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Despesas</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_expenses'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Despesas Pendentes</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['paid_expenses'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Despesas Pagas</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Valor Total</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-orange-600">R$ {{ number_format($stats['pending_amount'], 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Valor Pendente</p>
                </div>
            </div>
        </div>

        <!-- Despesas Recentes -->
        @if($recentExpenses->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Despesas Recentes</h4>
                    <a href="{{ route('expenses.index', ['credit_card_id' => $creditCard->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                        Ver todas →
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Vencimento
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Parcelas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentExpenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $expense->description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            R$ {{ number_format($expense->amount, 2, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $expense->due_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($expense->status === 'paid')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Pago
                                            </span>
                                        @elseif($expense->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Vencido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($expense->installments->count() > 1)
                                            {{ $expense->installments->where('status', 'paid')->count() }}/{{ $expense->installments->count() }}
                                        @else
                                            Única
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhuma despesa encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Este cartão ainda não possui despesas vinculadas.
                    </p>
                    @can('create-expense')
                        <div class="flex justify-start space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('expenses.create') }}" class="btn-primary-md align-icon-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Cadastrar Despesa</span>
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        @endif
    </div>
@endsection