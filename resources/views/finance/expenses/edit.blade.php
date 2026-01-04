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
                <span>Editar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Editar Despesa</h3>
            <div class="content-box-btn">
                @can('show-expense')
                    <a href="{{ route('expenses.show', $expense) }}" class="btn-info-md align-icon-btn">
                        <!-- Ícone eye (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>Visualizar</span>
                    </a>
                @endcan
            </div>
        </div>

        <x-alert />

        <form action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="description" class="form-label">Descrição *</label>
                <input type="text" name="description" id="description" class="form-input"
                    value="{{ old('description', $expense->description) }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="amount" class="form-label">Valor (R$) *</label>
                    <input type="text" name="amount" id="amount" class="form-input money-input"
                        value="{{ old('amount', number_format($expense->amount, 2, ',', '.')) }}" required>
                </div>

                <div class="mb-4">
                    <label for="due_date" class="form-label">Data de Vencimento *</label>
                    <input type="date" name="due_date" id="due_date" class="form-input"
                        value="{{ old('due_date', $expense->due_date?->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="periodicity" class="form-label">Periodicidade *</label>
                    <select name="periodicity" id="periodicity" class="form-input" required>
                        <option value="one-time" {{ old('periodicity', $expense->periodicity) == 'one-time' ? 'selected' : '' }}>Única</option>
                        <option value="monthly" {{ old('periodicity', $expense->periodicity) == 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="biweekly" {{ old('periodicity', $expense->periodicity) == 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                        <option value="bimonthly" {{ old('periodicity', $expense->periodicity) == 'bimonthly' ? 'selected' : '' }}>Bimestral</option>
                        <option value="semiannual" {{ old('periodicity', $expense->periodicity) == 'semiannual' ? 'selected' : '' }}>Semestral</option>
                        <option value="yearly" {{ old('periodicity', $expense->periodicity) == 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">Status *</label>
                    <select name="status" id="status" class="form-input" required>
                        <option value="pending" {{ old('status', $expense->status) == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="paid" {{ old('status', $expense->status) == 'paid' ? 'selected' : '' }}>Paga</option>
                    </select>
                </div>
            </div>

            <div id="payment-date-container" class="{{ old('status', $expense->status) == 'paid' ? '' : 'hidden' }} mb-4">
                <label for="payment_date" class="form-label">Data de Pagamento</label>
                <input type="date" name="payment_date" id="payment_date" class="form-input"
                    value="{{ old('payment_date', $expense->payment_date?->format('Y-m-d')) }}">
            </div>

            <div id="reason-container" class="{{ old('status', $expense->status) == 'pending' && $expense->reason_not_paid ? '' : 'hidden' }} mb-4">
                <label for="reason_not_paid" class="form-label">Motivo de Não Pagamento</label>
                <textarea name="reason_not_paid" id="reason_not_paid" rows="3" class="form-input"
                    placeholder="Descreva o motivo...">{{ old('reason_not_paid', $expense->reason_not_paid) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="credit_card_id" class="form-label">Cartão de Crédito (Opcional)</label>
                    <select name="credit_card_id" id="credit_card_id" class="form-input">
                        <option value="">Nenhum</option>
                        @foreach($creditCards as $card)
                            <option value="{{ $card->id }}" {{ old('credit_card_id', $expense->credit_card_id) == $card->id ? 'selected' : '' }}>
                                {{ $card->name }} - {{ $card->bank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="category_id" class="form-label">Categoria (Opcional)</label>
                    <select name="category_id" id="category_id" class="form-input">
                        <option value="">Sem categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
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
                        <span>Salvar</span>
                    </button>
                    <a href="{{ route('expenses.show', $expense) }}" class="btn-warning-md align-icon-btn">
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
