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
                <span>Editar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Editar Objetivo</h3>
            <div class="content-box-btn">
                @can('dashboard')
                    <a href="{{ route('wishlist.index') }}" class="btn-info-md align-icon-btn">
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

        <form action="{{ route('wishlist.update', $wishlist) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="form-label">Nome do Objetivo *</label>
                <input type="text" name="name" id="name" class="form-input"
                    placeholder="Ex: Viagem para a Europa" value="{{ old('name', $wishlist->name) }}" required>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Descrição</label>
                <textarea name="description" id="description" rows="3" class="form-input"
                    placeholder="Descreva seu objetivo...">{{ old('description', $wishlist->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="target_amount" class="form-label">Valor Alvo (R$) *</label>
                    <input type="text" name="target_amount" id="target_amount" class="form-input money-input"
                        placeholder="0,00" value="{{ old('target_amount', number_format($wishlist->target_amount, 2, ',', '.')) }}" required>
                </div>

                <div class="mb-4">
                    <label for="current_amount" class="form-label">Valor Já Economizado (R$)</label>
                    <input type="text" name="current_amount" id="current_amount" class="form-input money-input"
                        placeholder="0,00" value="{{ old('current_amount', number_format($wishlist->current_amount, 2, ',', '.')) }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="priority" class="form-label">Prioridade *</label>
                    <select name="priority" id="priority" class="form-input" required>
                        @foreach($priorities as $key => $value)
                            <option value="{{ $key }}" {{ old('priority', $wishlist->priority) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="target_date" class="form-label">Data Alvo</label>
                    <input type="date" name="target_date" id="target_date" class="form-input"
                        value="{{ old('target_date', $wishlist->target_date?->format('Y-m-d')) }}">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data esperada para atingir o objetivo (opcional)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-input">
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $wishlist->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea name="notes" id="notes" rows="3" class="form-input"
                        placeholder="Anotações adicionais...">{{ old('notes', $wishlist->notes) }}</textarea>
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
                        <span>Atualizar</span>
                    </button>
                    <a href="{{ route('wishlist.show', $wishlist) }}" class="btn-warning-md align-icon-btn">
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
