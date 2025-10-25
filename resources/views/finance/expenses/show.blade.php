@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('expenses.index') }}" class="breadcrumb-link">Despesas</a>
                <span>/</span>
                <span>Detalhes</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Detalhes da Despesa</h3>
            <div class="content-box-btn">
                @can('index-expense')
                    <a href="{{ route('expenses.index') }}" class="btn-info-md align-icon-btn">
                        <!-- Ícone queue-list (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                        <span>Listar</span>
                    </a>
                @endcan

                @can('edit-expense')
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone pencil-square (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
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
                        <button type="button" onclick="confirmDelete({{ $expense->id }})" class="btn-danger-md flex items-center space-x-1">
                            <!-- Ícone trash (Heroicons) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Apagar</span>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <x-alert />

        <!-- Alertas de Status -->
        @if($expense->is_overdue)
            <div class="mb-4 p-4 bg-red-500/10 dark:bg-red-500/10 border border-red-500 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="text-red-600 dark:text-red-400 font-semibold">Despesa Vencida</h4>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">Esta despesa está vencida. Regularize o pagamento o quanto antes.</p>
                    </div>
                </div>
            </div>
        @elseif($expense->is_due_soon)
            <div class="mb-4 p-4 bg-orange-500/10 dark:bg-orange-500/10 border border-orange-500 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div>
                        <h4 class="text-orange-600 dark:text-orange-400 font-semibold">Vencimento Próximo</h4>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">Esta despesa vence nos próximos 7 dias.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Informações Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Descrição</p>
                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $expense->description }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Valor</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $expense->amount_formatted }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Data de Vencimento</p>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $expense->due_date_formatted }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @if($expense->is_overdue)
                        <span class="px-3 py-1 text-sm rounded bg-red-500 text-white">Vencida</span>
                    @elseif($expense->status === 'paid')
                        <span class="px-3 py-1 text-sm rounded bg-green-500 text-white">Paga</span>
                    @else
                        <span class="px-3 py-1 text-sm rounded bg-yellow-500 text-white">Pendente</span>
                    @endif
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Periodicidade</p>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $expense->periodicity_translated }}</p>
            </div>

            @if($expense->payment_date)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Data de Pagamento</p>
                <p class="text-lg text-green-600 dark:text-green-500">{{ $expense->payment_date_formatted }}</p>
            </div>
            @endif

            @if($expense->creditCard)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Cartão de Crédito</p>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $expense->creditCard->name }} - {{ $expense->creditCard->bank }}</p>
            </div>
            @endif

            @if($expense->reason_not_paid)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Motivo de Não Pagamento</p>
                <p class="text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                    {{ $expense->reason_not_paid }}
                </p>
            </div>
            @endif
        </div>

        <!-- Ações Rápidas -->
        @if($expense->status === 'pending' || $expense->status === 'overdue')
        <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações Rápidas</h4>
            <div class="flex gap-4 flex-wrap">
                <button onclick="markExpenseAsPaid()" class="btn-success-md align-icon-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Marcar como Paga</span>
                </button>
                <button onclick="markExpenseAsOverdue()" class="btn-danger-md align-icon-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <span>Não Consegui Pagar</span>
                </button>
            </div>
        </div>
        @endif

        <!-- Informações de Parcelas -->
        @if($expense->hasInstallments())
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Parcelas ({{ $expense->num_installments }}x)</h4>
                @php
                    $stats = $expense->getInstallmentsStats();
                @endphp
                <div class="flex gap-3 text-sm">
                    <span class="text-green-600 dark:text-green-400">✓ Pagas: {{ $stats['paid'] }}</span>
                    <span class="text-yellow-600 dark:text-yellow-400">⏳ Pendentes: {{ $stats['pending'] }}</span>
                    @if($stats['overdue'] > 0)
                    <span class="text-red-600 dark:text-red-400">⚠ Vencidas: {{ $stats['overdue'] }}</span>
                    @endif
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr class="table-row-header">
                            <th class="table-header">#</th>
                            <th class="table-header">Valor</th>
                            <th class="table-header">Vencimento</th>
                            <th class="table-header">Status</th>
                            <th class="table-header center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expense->installments as $installment)
                        <tr class="table-row-body">
                            <td class="table-body">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600 dark:text-blue-500">
                                        {{ $installment->installment_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        de {{ $expense->num_installments }}
                                    </div>
                                </div>
                            </td>
                            <td class="table-body">
                                <span class="text-lg font-semibold">{{ $installment->amount_formatted }}</span>
                            </td>
                            <td class="table-body">
                                {{ $installment->due_date->format('d/m/Y') }}
                                @if($installment->payment_date)
                                    <br><span class="text-xs text-green-600 dark:text-green-400">Pago em: {{ $installment->payment_date->format('d/m/Y') }}</span>
                                @endif
                            </td>
                            <td class="table-body">
                                @if($installment->is_overdue)
                                    <span class="px-3 py-1 text-sm rounded bg-red-500 text-white">Vencida</span>
                                @elseif($installment->status === 'paid')
                                    <span class="px-3 py-1 text-sm rounded bg-green-500 text-white">Paga</span>
                                @else
                                    <span class="px-3 py-1 text-sm rounded bg-yellow-500 text-white">Pendente</span>
                                @endif
                            </td>
                            <td class="table-actions">
                                <div class="table-actions-align">
                                    @can('edit-expense')
                                        @if($installment->status === 'pending')
                                            <button onclick="markInstallmentAsPaid({{ $installment->id }})" 
                                                    class="btn-success-md align-icon-btn text-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>Pagar</span>
                                            </button>
                                        @else
                                            <button onclick="markInstallmentAsUnpaid({{ $installment->id }})" 
                                                    class="btn-warning-md align-icon-btn text-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span>Desfazer</span>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Informações do Sistema -->
        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Sistema</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Criado em:</span>
                    <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $expense->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Última atualização:</span>
                    <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $expense->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

    </div>
@endsection

