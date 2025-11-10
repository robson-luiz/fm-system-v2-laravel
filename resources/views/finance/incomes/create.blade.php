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
                <span>Cadastrar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Cadastrar Receita</h3>
            <div class="content-box-btn">
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

        <form method="POST" action="{{ route('incomes.store') }}" class="space-y-6">
            @csrf

            <!-- Informações Básicas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Básicas</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="description" class="form-label">Descrição *</label>
                        <input type="text" name="description" id="description" class="form-input" 
                               placeholder="Ex: Salário, Freelance, Venda..." 
                               value="{{ old('description') }}" required>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amount" class="form-label">Valor *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">R$</span>
                            <input type="text" name="amount" id="amount" class="form-input pl-10" 
                                   placeholder="0,00" 
                                   value="{{ old('amount') }}" required>
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="received_date" class="form-label">Data de Recebimento *</label>
                        <input type="date" name="received_date" id="received_date" class="form-input" 
                               value="{{ old('received_date', now()->format('Y-m-d')) }}" required>
                        @error('received_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="form-label">Categoria *</label>
                        <select name="category" id="category" class="form-input" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="form-label">Tipo *</label>
                        <select name="type" id="type" class="form-input" required>
                            <option value="">Selecione o tipo</option>
                            <option value="fixa" {{ old('type') == 'fixa' ? 'selected' : '' }}>Receita Fixa</option>
                            <option value="variavel" {{ old('type') == 'variavel' ? 'selected' : '' }}>Receita Variável</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informações Complementares -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Complementares</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="form-label">Status *</label>
                        <select name="status" id="status" class="form-input" required>
                            <option value="pendente" {{ old('status', 'pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="recebida" {{ old('status') == 'recebida' ? 'selected' : '' }}>Recebida</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="source" class="form-label">Fonte/Origem</label>
                        <input type="text" name="source" id="source" class="form-input" 
                               placeholder="Ex: Empresa XYZ, Cliente ABC..." 
                               value="{{ old('source') }}">
                        @error('source')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea name="notes" id="notes" rows="3" class="form-input" 
                                  placeholder="Informações adicionais sobre a receita...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('incomes.index') }}" class="btn-secondary-md">
                    Cancelar
                </a>
                <button type="submit" class="btn-success-md">
                    Cadastrar Receita
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Máscara de dinheiro para o campo valor
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            
            if (amountInput) {
                amountInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    
                    // Remove tudo que não é dígito
                    value = value.replace(/\D/g, '');
                    
                    // Aplica a máscara de moeda
                    value = (value / 100).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    e.target.value = value;
                });
            }
        });
    </script>
    @endpush
@endsection