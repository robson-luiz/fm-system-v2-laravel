@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Cartões de Crédito</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Listar Cartões de Crédito</h3>
            <div class="content-box-btn">
                @can('create-credit-card')
                    <a href="{{ route('credit-cards.create') }}" class="btn-success-md align-icon-btn">
                        <!-- Ícone plus-circle (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Cadastrar</span>
                    </a>
                @endcan
            </div>
        </div>

        <x-alert />

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-500/10 dark:bg-blue-500/10 border border-blue-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total de Cartões</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $stats['total'] }}</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-green-500/10 dark:bg-green-500/10 border border-green-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Cartões Ativos</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-500">{{ $stats['active'] }}</p>
                    </div>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-purple-500/10 dark:bg-purple-500/10 border border-purple-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Limite Total</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-500">R$ {{ number_format($stats['total_limit'], 2, ',', '.') }}</p>
                    </div>
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>

            <div class="bg-orange-500/10 dark:bg-orange-500/10 border border-orange-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Limite Disponível</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-500">R$ {{ number_format($stats['total_available'], 2, ',', '.') }}</p>
                    </div>
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('credit-cards.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="bank" class="form-label">Banco</label>
                    <input type="text" name="bank" id="bank" class="form-input" 
                           placeholder="Digite o nome do banco..." value="{{ request('bank') }}">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-input">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-primary-md flex items-center space-x-1">
                        <!-- Ícone magnifying-glass (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <span>Filtrar</span>
                    </button>

                    @if(request()->hasAny(['bank', 'status']))
                        <a href="{{ route('credit-cards.index') }}" class="btn-secondary-md flex items-center space-x-1">
                            <!-- Ícone x-mark (Heroicons) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span>Limpar</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lista de Cartões -->
        @if($creditCards->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                @foreach($creditCards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Header do Card -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $card->name }}</h3>
                                    <p class="text-blue-100 text-sm">{{ $card->bank }}</p>
                                    @if($card->last_four_digits)
                                        <p class="text-blue-100 text-xs mt-1">•••• •••• •••• {{ $card->last_four_digits }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($card->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ● Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ● Inativo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Conteúdo do Card -->
                        <div class="p-6">
                            <!-- Informações do Limite -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Limite Utilizado</span>
                                    <span class="text-sm font-medium">{{ $card->usage_percentage }}%</span>
                                </div>
                                
                                <!-- Barra de Progresso -->
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full" 
                                         style="width: {{ $card->usage_percentage }}%"></div>
                                </div>
                                
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>Disponível: R$ {{ number_format($card->available_limit, 2, ',', '.') }}</span>
                                    <span>Total: R$ {{ number_format($card->card_limit, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Informações de Datas -->
                            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Fechamento:</span>
                                    <p class="font-medium">Dia {{ $card->closing_day }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Vencimento:</span>
                                    <p class="font-medium">Dia {{ $card->due_day }}</p>
                                </div>
                            </div>

                            <!-- Próximo Vencimento -->
                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Próximo Vencimento:</span>
                                    <span class="text-sm font-medium">
                                        {{ $card->next_due_date->format('d/m/Y') }}
                                        @if($card->days_to_due <= 7)
                                            <span class="text-red-600 ml-1">({{ $card->days_to_due }} dias)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Melhor Dia para Compra -->
                            @if($card->best_purchase_day || isset($card->calculated_best_day))
                                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-green-700 dark:text-green-400">Melhor dia para compra:</span>
                                        <span class="text-sm font-medium text-green-800 dark:text-green-300">
                                            Dia {{ $card->best_purchase_day ?? $card->calculated_best_day }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Estatísticas -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                {{ $card->expenses_count }} despesa(s) vinculada(s)
                            </div>
                        </div>

                        <!-- Footer com Ações -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-between items-center">
                            <div class="flex space-x-2">
                                @can('show-credit-card')
                                    <a href="{{ route('credit-cards.show', $card) }}" 
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('edit-credit-card')
                                    <a href="{{ route('credit-cards.edit', $card) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('edit-credit-card')
                                    <form method="POST" action="{{ route('credit-cards.toggle-status', $card) }}" 
                                          style="display: inline;">
                                        @csrf
                                        <button type="submit" 
                                                class="text-{{ $card->is_active ? 'red' : 'green' }}-600 hover:text-{{ $card->is_active ? 'red' : 'green' }}-800">
                                            @if($card->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            @can('destroy-credit-card')
                                <form method="POST" action="{{ route('credit-cards.destroy', $card) }}" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir este cartão?')" 
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="flex justify-center">
                {{ $creditCards->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum cartão encontrado</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['bank', 'status']))
                        Nenhum cartão corresponde aos filtros aplicados.
                    @else
                        Comece cadastrando seu primeiro cartão de crédito.
                    @endif
                </p>
                @can('create-credit-card')
                    <div class="mt-6">
                        <a href="{{ route('credit-cards.create') }}" class="btn-primary-md align-icon-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Cadastrar Cartão</span>
                        </a>
                    </div>
                @endcan
            </div>
        @endif
    </div>
@endsection