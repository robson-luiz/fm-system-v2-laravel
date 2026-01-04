@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Despesas</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Listar Despesas</h3>
            <div class="content-box-btn">
                @can('create-expense')
                    <a href="{{ route('expenses.create') }}" class="btn-success-md align-icon-btn">
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
            <div class="bg-yellow-500/10 dark:bg-yellow-500/10 border border-yellow-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pendentes</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">R$ {{ number_format($stats['total_pending'], 2, ',', '.') }}</p>
                    </div>
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-green-500/10 dark:bg-green-500/10 border border-green-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pagas no Mês</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-500">R$ {{ number_format($stats['total_paid_month'], 2, ',', '.') }}</p>
                    </div>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-red-500/10 dark:bg-red-500/10 border border-red-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Vencidas</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-500">{{ $stats['overdue_count'] }}</p>
                    </div>
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-orange-500/10 dark:bg-orange-500/10 border border-orange-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Vencem em 7 dias</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-500">{{ $stats['due_soon_count'] }}</p>
                    </div>
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Início Formulário de Pesquisa -->
        <form class="form-search">
            <select name="status" class="form-input">
                <option value="">Todos os status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagas</option>
            </select>

            <select name="periodicity" class="form-input">
                <option value="">Todas as periodicidades</option>
                <option value="one-time" {{ request('periodicity') == 'one-time' ? 'selected' : '' }}>Única</option>
                <option value="monthly" {{ request('periodicity') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                <option value="biweekly" {{ request('periodicity') == 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                <option value="bimonthly" {{ request('periodicity') == 'bimonthly' ? 'selected' : '' }}>Bimestral</option>
                <option value="semiannual" {{ request('periodicity') == 'semiannual' ? 'selected' : '' }}>Semestral</option>
                <option value="yearly" {{ request('periodicity') == 'yearly' ? 'selected' : '' }}>Anual</option>
            </select>

            <select name="credit_card_id" class="form-input">
                <option value="">Todos os cartões</option>
                @foreach($creditCards as $card)
                    <option value="{{ $card->id }}" {{ request('credit_card_id') == $card->id ? 'selected' : '' }}>
                        {{ $card->name }}
                    </option>
                @endforeach
            </select>

            <select name="category_id" class="form-input">
                <option value="">Todas as categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <input type="month" name="month" class="form-input" value="{{ request('month') }}">

            <div class="flex gap-1">
                <button type="submit" class="btn-primary-md flex items-center space-x-1">
                    <!-- Ícone magnifying-glass (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Pesquisar</span>
                </button>
                <a href="{{ route('expenses.index') }}" class="btn-warning-md flex items-center space-x-1">
                    <!-- Ícone trash (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Limpar</span>
                </a>
            </div>
        </form>
        <!-- Fim Formulário de Pesquisa -->

        <div class="table-container mt-6">
            <table class="table">
                <thead>
                    <tr class="table-row-header">
                        <th class="table-header">Descrição</th>
                        <th class="table-header">Valor</th>
                        <th class="table-header hidden lg:table-cell">Vencimento</th>
                        <th class="table-header hidden lg:table-cell">Status</th>
                        <th class="table-header hidden lg:table-cell">Parcelas</th>
                        <th class="table-header center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="table-row-body">
                            <td class="table-body">
                                <div class="flex flex-col gap-1">
                                    <span>{{ $expense->description }}</span>
                                    @if($expense->category)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full w-fit" 
                                              style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}; border: 1px solid {{ $expense->category->color }}40;">
                                            <span>{{ $expense->category->icon }}</span>
                                            <span class="font-medium">{{ $expense->category->name }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="table-body">{{ $expense->amount_formatted }}</td>
                            <td class="table-body hidden lg:table-cell">{{ $expense->due_date_formatted }}</td>
                            <td class="table-body hidden lg:table-cell">
                                @if($expense->is_overdue)
                                    <span class="px-2 py-1 text-xs rounded bg-red-500 text-white">Vencida</span>
                                @elseif($expense->status === 'paid')
                                    <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Paga</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-500 text-white">Pendente</span>
                                @endif
                            </td>
                            <td class="table-body hidden lg:table-cell">
                                @if($expense->hasInstallments())
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $expense->installments_summary }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">À vista</span>
                                @endif
                            </td>
                            <td class="table-actions">
                                <div class="table-actions-align">
                                    @can('show-expense')
                                        <a href="{{ route('expenses.show', $expense) }}" class="btn-primary-md align-icon-btn">
                                            <!-- Ícone eye (Heroicons) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>Visualizar</span>
                                        </a>
                                    @endcan

                                    @can('edit-expense')
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn-warning-md table-md-hidden">
                                            <!-- Ícone pencil-square (Heroicons) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Editar</span>
                                        </a>
                                    @endcan

                                    @can('destroy-expense')
                                        <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $expense->id }})" class="btn-danger-md table-md-hidden">
                                                <!-- Ícone trash (Heroicons) -->
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                <span>Apagar</span>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="alert-warning">
                                    Nenhuma despesa encontrada!
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2 p-3">
                {{ $expenses->onEachSide(1)->links() }}
            </div>
        </div>

    </div>
@endsection
