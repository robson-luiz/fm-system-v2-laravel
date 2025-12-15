@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Wishlist</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('wishlist.index') }}" class="breadcrumb-link">Wishlist</a>
                <span>/</span>
                <span>{{ $wishlist->name }}</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">{{ $wishlist->name }}</h3>
            <div class="content-box-btn flex space-x-2">
                <a href="{{ route('wishlist.index') }}" class="btn-secondary-md align-icon-btn">
                    <!-- Ícone arrow-left (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Voltar</span>
                </a>
                @can('dashboard')
                    <a href="{{ route('wishlist.edit', $wishlist) }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone pencil (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Editar</span>
                    </a>
                @endcan
            </div>
        </div>

        <x-alert />

        <!-- Card Principal do Objetivo -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg p-8 text-white mb-6 shadow-lg">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold">{{ $wishlist->name }}</h2>
                    @if($wishlist->description)
                        <p class="text-purple-100 text-lg mt-2">{{ $wishlist->description }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if($wishlist->status == 'em_progresso')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            ⏳ Em Progresso
                        </span>
                    @elseif($wishlist->status == 'concluida')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Concluída
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ❌ Cancelada
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informações do Progresso -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-purple-100 text-sm">Valor Economizado</p>
                    <p class="text-2xl font-bold">{{ $wishlist->formatted_current_amount }}</p>
                </div>
                <div>
                    <p class="text-purple-100 text-sm">Faltam</p>
                    <p class="text-2xl font-bold">{{ $wishlist->formatted_remaining_amount }}</p>
                </div>
                <div>
                    <p class="text-purple-100 text-sm">Meta Total</p>
                    <p class="text-2xl font-bold">{{ $wishlist->formatted_target_amount }}</p>
                </div>
            </div>

            <!-- Barra de Progresso -->
            <div class="mt-4">
                <div class="w-full bg-purple-400/30 rounded-full h-3">
                    <div class="bg-white h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $wishlist->progress_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-purple-100 mt-1">
                    <span>Progresso: {{ $wishlist->progress_percentage }}%</span>
                    <span>Restam: {{ 100 - $wishlist->progress_percentage }}%</span>
                </div>
            </div>
        </div>

        <!-- Informações Detalhadas -->
        <!-- Informações Detalhadas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Informações Gerais -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Gerais</h4>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Prioridade:</span>
                        @if($wishlist->priority == 'alta')
                            <span class="font-medium px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 rounded text-sm">Alta</span>
                        @elseif($wishlist->priority == 'media')
                            <span class="font-medium px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded text-sm">Média</span>
                        @else
                            <span class="font-medium px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded text-sm">Baixa</span>
                        @endif
                    </div>
                    
                    @if($wishlist->target_date)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Data Alvo:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $wishlist->target_date->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Tempo Restante:</span>
                            @php
                                $daysRemaining = now()->diffInDays($wishlist->target_date, false);
                            @endphp
                            @if($daysRemaining > 0)
                                <span class="font-medium text-green-600">{{ $daysRemaining }} dias</span>
                            @elseif($daysRemaining < 0)
                                <span class="font-medium text-red-600">{{ abs($daysRemaining) }} dias atrasado</span>
                            @else
                                <span class="font-medium text-orange-600">Hoje!</span>
                            @endif
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Data de Cadastro:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $wishlist->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Última Atualização:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $wishlist->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Análise de Viabilidade Resumida -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Análise de Viabilidade</h4>
                
                <div class="space-y-4">
                    @php
                        $statusColors = [
                            'muito_viavel' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300', 'icon' => '✅'],
                            'viavel' => ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-800 dark:text-blue-300', 'icon' => '👍'],
                            'moderado' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300', 'icon' => '⚠️'],
                            'dificil' => ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-800 dark:text-orange-300', 'icon' => '🔶'],
                            'inviavel' => ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-800 dark:text-red-300', 'icon' => '❌'],
                        ];
                        $currentStatus = $statusColors[$analysis['viability_status']] ?? $statusColors['moderado'];
                    @endphp
                    
                    <div class="{{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} rounded-lg p-4">
                        <p class="text-lg font-semibold">{{ $currentStatus['icon'] }} Viabilidade: {{ $analysis['viability_percentage'] }}%</p>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Saldo Mensal Médio:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($analysis['monthly_balance'], 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Valor Mensal Necessário:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($analysis['monthly_amount_needed'], 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Meses Necessários:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            @if($analysis['months_needed'] >= 2147483647)
                                ∞
                            @else
                                {{ $analysis['months_needed'] }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Previsão de Conclusão:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            @if($analysis['estimated_completion_date'])
                                {{ \Carbon\Carbon::parse($analysis['estimated_completion_date'])->format('m/Y') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impacto no Orçamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 mb-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">💰 Impacto no Orçamento</h4>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Percentual do Saldo Mensal</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ number_format($analysis['impact_analysis']['percentage'], 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-600 mb-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ min(100, $analysis['impact_analysis']['percentage']) }}%"></div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $analysis['impact_analysis']['message'] }}</p>
            </div>
        </div>

        <!-- Recomendações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 mb-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">💡 Recomendações</h4>
            
            <div class="space-y-3">
                @foreach($analysis['recommendations'] as $recommendation)
                    @php
                        $typeColors = [
                            'success' => 'bg-green-50 dark:bg-green-900 border-green-200 dark:border-green-700 text-green-800 dark:text-green-300',
                            'info' => 'bg-blue-50 dark:bg-blue-900 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-300',
                            'warning' => 'bg-yellow-50 dark:bg-yellow-900 border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300',
                            'danger' => 'bg-red-50 dark:bg-red-900 border-red-200 dark:border-red-700 text-red-800 dark:text-red-300',
                        ];
                    @endphp
                    <div class="p-3 rounded-lg border {{ $typeColors[$recommendation['type']] ?? $typeColors['info'] }}">
                        <p class="text-sm">{{ $recommendation['icon'] }} {{ $recommendation['message'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($wishlist->notes)
            <!-- Observações -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">📝 Observações</h4>
                <p class="text-gray-600 dark:text-gray-400">{{ $wishlist->notes }}</p>
            </div>
        @endif
    </div>
@endsection
