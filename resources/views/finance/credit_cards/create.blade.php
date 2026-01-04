@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Financeiro</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('credit-cards.index') }}" class="breadcrumb-link">Cartões de Crédito</a>
                <span>/</span>
                <span>Cadastrar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Cadastrar Cartão de Crédito</h3>
            <div class="content-box-btn">
                <a href="{{ route('credit-cards.index') }}" class="btn-secondary-md align-icon-btn">
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

        <form method="POST" action="{{ route('credit-cards.store') }}" class="space-y-6">
            @csrf

            <!-- Informações Básicas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Básicas</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Nome do Cartão *</label>
                        <input type="text" name="name" id="name" class="form-input" 
                               placeholder="Ex: Cartão Nubank, Visa Gold..." 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank" class="form-label">Banco Emissor *</label>
                        <input type="text" name="bank" id="bank" class="form-input" 
                               placeholder="Ex: Nubank, Bradesco, Itaú..." 
                               value="{{ old('bank') }}" required>
                        @error('bank')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_four_digits" class="form-label">Últimos 4 Dígitos</label>
                        <input type="text" name="last_four_digits" id="last_four_digits" class="form-input" 
                               placeholder="1234" maxlength="4" 
                               value="{{ old('last_four_digits') }}">
                        @error('last_four_digits')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Opcional. Apenas para identificação visual.
                        </p>
                    </div>

                    <div>
                        <label for="card_limit" class="form-label">Limite do Cartão *</label>
                        <input type="text" name="card_limit" id="card_limit" class="form-input money-mask" 
                               placeholder="R$ 0,00" 
                               value="{{ old('card_limit') }}" required>
                        @error('card_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Datas Importantes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Datas Importantes</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="closing_day" class="form-label">Dia de Fechamento *</label>
                        <select name="closing_day" id="closing_day" class="form-input" required>
                            <option value="">Selecione o dia</option>
                            @for($day = 1; $day <= 31; $day++)
                                <option value="{{ $day }}" {{ old('closing_day') == $day ? 'selected' : '' }}>
                                    Dia {{ $day }}
                                </option>
                            @endfor
                        </select>
                        @error('closing_day')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Dia que a fatura fecha no mês.
                        </p>
                    </div>

                    <div>
                        <label for="due_day" class="form-label">Dia de Vencimento *</label>
                        <select name="due_day" id="due_day" class="form-input" required>
                            <option value="">Selecione o dia</option>
                            @for($day = 1; $day <= 31; $day++)
                                <option value="{{ $day }}" {{ old('due_day') == $day ? 'selected' : '' }}>
                                    Dia {{ $day }}
                                </option>
                            @endfor
                        </select>
                        @error('due_day')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Dia que a fatura vence no mês.
                        </p>
                    </div>

                    <div>
                        <label for="best_purchase_day" class="form-label">Melhor Dia para Compra</label>
                        <select name="best_purchase_day" id="best_purchase_day" class="form-input">
                            <option value="">Calcular automaticamente</option>
                            @for($day = 1; $day <= 31; $day++)
                                <option value="{{ $day }}" {{ old('best_purchase_day') == $day ? 'selected' : '' }}>
                                    Dia {{ $day }}
                                </option>
                            @endfor
                        </select>
                        @error('best_purchase_day')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Deixe vazio para calcular automaticamente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informações Financeiras -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Financeiras</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="interest_rate" class="form-label">Taxa de Juros Mensal (%)</label>
                        <input type="number" name="interest_rate" id="interest_rate" class="form-input" 
                               placeholder="0.00" step="0.01" min="0" max="100"
                               value="{{ old('interest_rate') }}">
                        @error('interest_rate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Taxa de juros aplicada no rotativo.
                        </p>
                    </div>

                    <div>
                        <label for="annual_fee" class="form-label">Anuidade</label>
                        <input type="text" name="annual_fee" id="annual_fee" class="form-input money-mask" 
                               placeholder="R$ 0,00" 
                               value="{{ old('annual_fee') }}">
                        @error('annual_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Valor da anuidade cobrada pelo cartão.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Status</h4>
                
                <div class="flex items-center">
                    <!-- Campo hidden para garantir que sempre seja enviado um valor -->
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                           {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                        Cartão ativo
                    </label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Desmarque se o cartão estiver cancelado ou bloqueado.
                </p>
            </div>

            <!-- Dica Informativa -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <svg class="flex-shrink-0 w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Dica</h3>
                        <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                            <p>O <strong>melhor dia para compra</strong> é normalmente logo após o vencimento da fatura, pois você terá mais tempo para pagar. Se não especificar, calcularemos automaticamente como o dia seguinte ao vencimento.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-start space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-success-md">
                    Cadastrar Cartão
                </button>
                <a href="{{ route('credit-cards.index') }}" class="btn-secondary-md">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Validação em tempo real dos últimos 4 dígitos
        document.getElementById('last_four_digits').addEventListener('input', function(e) {
            // Remover caracteres não numéricos
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Auto-sugestão do melhor dia para compra
        document.getElementById('due_day').addEventListener('change', function() {
            const dueDay = parseInt(this.value);
            const bestDaySelect = document.getElementById('best_purchase_day');
            
            if (dueDay && bestDaySelect.value === '') {
                let suggestedDay = dueDay + 1;
                if (suggestedDay > 31) {
                    suggestedDay = 1;
                }
                
                // Sugestão visual (não seleciona automaticamente)
                bestDaySelect.style.borderColor = '#10B981';
                const placeholder = bestDaySelect.querySelector('option[value=""]');
                placeholder.textContent = `Sugerido: Dia ${suggestedDay} (automático)`;
            }
        });
    </script>
    @endpush
@endsection