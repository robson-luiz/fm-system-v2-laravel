@extends('layouts.admin')

@section('content')

<div class="container-fluid px-4 py-6 mx-auto">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Wishlist</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Título e Botão --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">🎯 Wishlist Inteligente</h1>
            <p class="text-gray-600 dark:text-gray-400">Planeje e alcance seus objetivos financeiros</p>
        </div>
        <a href="{{ route('wishlist.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg mt-4 md:mt-0">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Objetivo
        </a>
    </div>

    {{-- Cards de Estatísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total de Objetivos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Em Progresso</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['em_progresso'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Concluídas</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['concluidas'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Valor Total</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">R$ {{ number_format($stats['total_valor'], 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('wishlist.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Todos</option>
                    <option value="em_progresso" {{ request('status') == 'em_progresso' ? 'selected' : '' }}>Em Progresso</option>
                    <option value="concluida" {{ request('status') == 'concluida' ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prioridade</label>
                <select name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Todas</option>
                    <option value="alta" {{ request('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="media" {{ request('priority') == 'media' ? 'selected' : '' }}>Média</option>
                    <option value="baixa" {{ request('priority') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                </select>
            </div>
            
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    🔍 Filtrar
                </button>
                <a href="{{ route('wishlist.index') }}" class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg">
                    🔄 Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- Grid de Cards --}}
    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    {{-- Header do Card --}}
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $wishlist->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($wishlist->description, 60) }}</p>
                            </div>
                            
                            {{-- Badge de Prioridade --}}
                            @if($wishlist->priority == 'alta')
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded dark:bg-red-900 dark:text-red-300">Alta</span>
                            @elseif($wishlist->priority == 'media')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded dark:bg-yellow-900 dark:text-yellow-300">Média</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded dark:bg-gray-700 dark:text-gray-300">Baixa</span>
                            @endif
                        </div>

                        {{-- Valores --}}
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Progresso</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $wishlist->progress_percentage }}%</span>
                            </div>
                            
                            {{-- Barra de Progresso --}}
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $wishlist->progress_percentage }}%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $wishlist->formatted_current_amount }}</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $wishlist->formatted_target_amount }}</span>
                            </div>
                        </div>

                        {{-- Data Alvo --}}
                        @if($wishlist->target_date)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                📅 Data alvo: {{ $wishlist->target_date->format('d/m/Y') }}
                            </p>
                        @endif

                        {{-- Badge de Status --}}
                        <div class="mb-4">
                            @if($wishlist->status == 'em_progresso')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    ⏳ Em Progresso
                                </span>
                            @elseif($wishlist->status == 'concluida')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    ✅ Concluída
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    ❌ Cancelada
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Footer do Card --}}
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-between items-center">
                        <a href="{{ route('wishlist.show', $wishlist) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            Ver Detalhes →
                        </a>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('wishlist.edit', $wishlist) }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            <button onclick="deleteWishlist({{ $wishlist->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $wishlists->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum objetivo cadastrado</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comece criando seu primeiro objetivo financeiro.</p>
            <div class="mt-6">
                <a href="{{ route('wishlist.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Criar Objetivo
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
