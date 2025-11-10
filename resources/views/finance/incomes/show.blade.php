@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('incomes.index') }}" class="breadcrumb-link">Receitas</a>
                <span>/</span>
                <span>Detalhes</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Detalhes da Receita</h3>
            <div class="content-box-btn flex space-x-2">
                @can('edit-income')
                    <a href="{{ route('incomes.edit', $income) }}" class="btn-primary-md align-icon-btn">
                        <!-- Ícone pencil (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Editar</span>
                    </a>
                @endcan

                <a href="{{ route('incomes.index') }}" class="btn-secondary-md align-icon-btn">
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

        <!-- Card Principal da Receita -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <!-- Header do Card -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-6 text-white">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold mb-2">{{ $income->description }}</h1>
                        <div class="flex flex-wrap gap-3 text-sm">
                            <span class="bg-green-500/20 px-3 py-1 rounded-full">
                                {{ $income->category }}
                            </span>
                            <span class="bg-green-500/20 px-3 py-1 rounded-full">
                                {{ $income->type === 'fixa' ? 'Receita Fixa' : 'Receita Variável' }}
                            </span>
                            @if($income->source)
                                <span class="bg-green-500/20 px-3 py-1 rounded-full">
                                    {{ $income->source }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        @if($income->status === 'recebida')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Recebida
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Pendente
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Valor Principal -->
            <div class="px-6 py-8 text-center bg-gray-50 dark:bg-gray-700/50">
                <div class="text-4xl font-bold text-green-600 dark:text-green-500 mb-2">
                    {{ $income->formatted_amount }}
                </div>
                <p class="text-gray-600 dark:text-gray-400">Valor da Receita</p>
            </div>

            <!-- Informações Detalhadas -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Data de Recebimento -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 dark:bg-blue-900/50 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Data de Recebimento</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $income->received_date->format('d/m/Y') }}
                                    @if($income->is_overdue)
                                        <span class="text-red-600 text-xs ml-1">(Atrasada)</span>
                                    @elseif($income->status === 'pendente' && $income->days_to_receive >= 0 && $income->days_to_receive <= 7)
                                        <span class="text-orange-600 text-xs ml-1">({{ $income->days_to_receive }} dias)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Categoria -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-purple-100 dark:bg-purple-900/50 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Categoria</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $income->category }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-indigo-100 dark:bg-indigo-900/50 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tipo</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $income->type === 'fixa' ? 'Receita Fixa' : 'Receita Variável' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($income->source)
                        <!-- Fonte/Origem -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-teal-100 dark:bg-teal-900/50 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Fonte/Origem</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $income->source }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Data de Cadastro -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gray-100 dark:bg-gray-600 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Cadastrado em</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $income->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($income->updated_at->ne($income->created_at))
                        <!-- Data de Atualização -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-yellow-100 dark:bg-yellow-900/50 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Última atualização</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $income->updated_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($income->notes)
                    <!-- Observações -->
                    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 dark:bg-blue-800/50 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-1">Observações</p>
                                <p class="text-sm text-blue-800 dark:text-blue-300 whitespace-pre-wrap">{{ $income->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ações -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                @can('edit-income')
                    <form method="POST" action="{{ route('incomes.toggle-status', $income) }}">
                        @csrf
                        <button type="submit" class="btn-{{ $income->status === 'recebida' ? 'warning' : 'success' }}-md">
                            @if($income->status === 'recebida')
                                Marcar como Pendente
                            @else
                                Marcar como Recebida
                            @endif
                        </button>
                    </form>
                @endcan
            </div>

            <div class="flex space-x-3">
                @can('destroy-income')
                    <form method="POST" action="{{ route('incomes.destroy', $income) }}" 
                          onsubmit="return confirm('Tem certeza que deseja excluir esta receita? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-md">
                            Excluir Receita
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection