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
                <span>Cadastrar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Cadastrar Despesa</h3>
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
            </div>
        </div>

        <x-alert />

        <form id="expense-form" action="{{ route('expenses.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="description" class="form-label">Descrição *</label>
                <input type="text" name="description" id="description" class="form-input"
                    placeholder="Ex: Conta de energia" value="{{ old('description') }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="amount" class="form-label">Valor (R$) *</label>
                    <input type="text" name="amount" id="amount" class="form-input money-input"
                        placeholder="0,00" value="{{ old('amount') }}" required>
                </div>

                <div class="mb-4">
                    <label for="due_date" class="form-label">Data de Vencimento *</label>
                    <input type="date" name="due_date" id="due_date" class="form-input"
                        value="{{ old('due_date') }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="periodicity" class="form-label">Periodicidade *</label>
                    <select name="periodicity" id="periodicity" class="form-input" required>
                        <option value="one-time" {{ old('periodicity') == 'one-time' ? 'selected' : '' }}>Única</option>
                        <option value="monthly" {{ old('periodicity') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="biweekly" {{ old('periodicity') == 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                        <option value="bimonthly" {{ old('periodicity') == 'bimonthly' ? 'selected' : '' }}>Bimestral</option>
                        <option value="semiannual" {{ old('periodicity') == 'semiannual' ? 'selected' : '' }}>Semestral</option>
                        <option value="yearly" {{ old('periodicity') == 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="num_installments" class="form-label">Número de Parcelas</label>
                    <input type="number" name="num_installments" id="num_installments" min="1" max="60" 
                        class="form-input" placeholder="1" value="{{ old('num_installments', 1) }}">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deixe 1 para despesa única</p>
                </div>
            </div>

            <!-- Toggle: Parcelas Iguais vs Personalizadas -->
            <div id="installment-type-container" class="hidden mb-4">
                <label class="form-label">Tipo de Parcelamento</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="installment_type" id="installment_type_equal" value="equal" 
                            class="form-radio text-blue-600" checked>
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Parcelas Iguais</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="installment_type" id="installment_type_custom" value="custom" 
                            class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Parcelas Personalizadas</span>
                    </label>
                </div>
            </div>

            <!-- Container para Parcelas Personalizadas -->
            <div id="custom-installments-container" class="hidden mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="form-label mb-0">Valores das Parcelas</label>
                    <div id="installments-sum-info" class="text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Soma: </span>
                        <span id="installments-sum" class="font-semibold text-gray-900 dark:text-gray-100">R$ 0,00</span>
                        <span class="mx-2 text-gray-400">|</span>
                        <span class="text-gray-600 dark:text-gray-400">Total: </span>
                        <span id="installments-total" class="font-semibold text-gray-900 dark:text-gray-100">R$ 0,00</span>
                        <span id="installments-difference" class="ml-2"></span>
                    </div>
                </div>
                <div id="custom-installments-fields" class="space-y-3 max-h-96 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <!-- Campos gerados dinamicamente pelo JavaScript -->
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="credit_card_id" class="form-label">Cartão de Crédito (Opcional)</label>
                    <select name="credit_card_id" id="credit_card_id" class="form-input">
                        <option value="">Nenhum</option>
                        @foreach($creditCards as $card)
                            <option value="{{ $card->id }}" {{ old('credit_card_id') == $card->id ? 'selected' : '' }}>
                                {{ $card->name }} - {{ $card->bank }} (Limite: {{ $card->available_limit_formatted }})
                            </option>
                        @endforeach
                    </select>
                    @if($creditCards->isEmpty())
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Você ainda não possui cartões cadastrados.
                        </p>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="category_id" class="form-label">Categoria (Opcional)</label>
                    <select name="category_id" id="category_id" class="form-input">
                        <option value="">Sem categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Informação sobre Parcelas -->
            <div id="installment-info" class="hidden mb-4 p-4 bg-blue-500/10 dark:bg-blue-500/10 border border-blue-500 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-blue-600 dark:text-blue-400 font-medium">Despesa Parcelada</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            Ao cadastrar com mais de 1 parcela, serão criadas <span id="installment-count" class="font-semibold">1</span> despesas, 
                            cada uma com valor de aproximadamente <span id="installment-amount" class="font-semibold">R$ 0,00</span>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <div class="flex gap-2">
                    <button type="submit" class="btn-success-md align-icon-btn">
                        <!-- Ícone check (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Cadastrar</span>
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone x-mark (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        <span>Cancelar</span>                    
                    </a> 
                </div>
            </div>
        </form>
    </div>
@endsection
