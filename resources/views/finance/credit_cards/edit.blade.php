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
                <a href="{{ route('credit-cards.show', $creditCard) }}" class="breadcrumb-link">{{ $creditCard->name }}</a>
                <span>/</span>
                <span>Editar</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Editar Cartão de Crédito</h3>
            <div class="content-box-btn">
                <a href="{{ route('credit-cards.show', $creditCard) }}" class="btn-secondary-md align-icon-btn">
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

        <form method="POST" action="{{ route('credit-cards.update', $creditCard) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Básicas</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Nome do Cartão *</label>
                        <input type="text" name="name" id="name" class="form-input" 
                               placeholder="Ex: Cartão Nubank, Visa Gold..." 
                               value="{{ old('name', $creditCard->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank" class="form-label">Banco Emissor *</label>
                        <input type="text" name="bank" id="bank" class="form-input" 
                               placeholder="Ex: Nubank, Bradesco, Itaú..." 
                               value="{{ old('bank', $creditCard->bank) }}" required>
                        @error('bank')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_four_digits" class="form-label">Últimos 4 Dígitos</label>
                        <input type="text" name="last_four_digits" id="last_four_digits" class="form-input" 
                               placeholder="1234" maxlength="4" 
                               value="{{ old('last_four_digits', $creditCard->last_four_digits) }}">
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
                               value="{{ old('card_limit', number_format($creditCard->card_limit, 2, ',', '.')) }}" required>
                        @error('card_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Limite Disponível -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Controle de Limite</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="available_limit" class="form-label">Limite Disponível *</label>
                        <input type="text" name="available_limit" id="available_limit" class="form-input money-mask" 
                               placeholder="R$ 0,00" 
                               value="{{ old('available_limit', number_format($creditCard->available_limit, 2, ',', '.')) }}" required>
                        @error('available_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-2">
                            <label class="flex items-center">
                                <input type="hidden" name="auto_calculate_limit" value="0">
                                <input type="checkbox" name="auto_calculate_limit" id="auto_calculate_limit" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       {{ old('auto_calculate_limit', $creditCard->auto_calculate_limit) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    Calcular automaticamente com base nas despesas
                                </span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Ajuste manualmente ou deixe o sistema calcular automaticamente.
                        </p>
                    </div>

                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Uso Atual do Limite</p>
                            <div class="relative w-24 h-24">
                                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="3" 
                                          fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="text-blue-600" stroke="currentColor" stroke-width="3" fill="none" 
                                          stroke-dasharray="{{ $creditCard->card_limit > 0 ? round(($creditCard->used_limit / $creditCard->card_limit) * 100, 1) : 0 }}, 100"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $creditCard->card_limit > 0 ? round(($creditCard->used_limit / $creditCard->card_limit) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                R$ {{ number_format($creditCard->used_limit, 2, ',', '.') }} usado
                            </p>
                        </div>
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
                                <option value="{{ $day }}" {{ old('closing_day', $creditCard->closing_day) == $day ? 'selected' : '' }}>
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
                                <option value="{{ $day }}" {{ old('due_day', $creditCard->due_day) == $day ? 'selected' : '' }}>
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
                                <option value="{{ $day }}" {{ old('best_purchase_day', $creditCard->best_purchase_day) == $day ? 'selected' : '' }}>
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
                               value="{{ old('interest_rate', $creditCard->interest_rate) }}">
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
                               value="{{ old('annual_fee', number_format($creditCard->annual_fee, 2, ',', '.')) }}">
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
                           {{ old('is_active', $creditCard->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                        Cartão ativo
                    </label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Desmarque se o cartão estiver cancelado ou bloqueado.
                </p>
            </div>

            <!-- Alerta sobre Despesas Vinculadas -->
            @if($creditCard->expenses()->count() > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Atenção</h3>
                            <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Este cartão possui <strong>{{ $creditCard->expenses()->count() }} despesa(s)</strong> vinculada(s). Alterações nos limites podem afetar a precisão dos cálculos financeiros.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botões de Ação -->
            <div class="flex justify-start space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-warning-md">
                    Atualizar
                </button>
                <a href="{{ route('credit-cards.show', $creditCard) }}" class="btn-secondary-md">
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

        // Validação do limite disponível vs limite total
        const cardLimitInput = document.getElementById('card_limit');
        const availableLimitInput = document.getElementById('available_limit');
        const autoCalculateCheckbox = document.getElementById('auto_calculate_limit');

        function validateLimits() {
            const cardLimit = parseFloat(cardLimitInput.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
            const availableLimit = parseFloat(availableLimitInput.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
            
            if (availableLimit > cardLimit) {
                availableLimitInput.style.borderColor = '#EF4444';
                availableLimitInput.setCustomValidity('O limite disponível não pode ser maior que o limite total');
            } else {
                availableLimitInput.style.borderColor = '';
                availableLimitInput.setCustomValidity('');
            }
        }

        function calculateAvailableLimit() {
            if (autoCalculateCheckbox.checked) {
                const cardLimit = parseFloat(cardLimitInput.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                // Aqui podemos buscar via AJAX o valor usado atual
                // Por enquanto, vamos usar o valor atual mostrado na tela
                const usedLimit = {{ $creditCard->used_limit ?? 0 }};
                const availableLimit = Math.max(0, cardLimit - usedLimit);
                
                // Formatar e definir o valor
                availableLimitInput.value = window.formatMoney((availableLimit * 100).toString());
                validateLimits();
            }
        }

        // Event listeners
        autoCalculateCheckbox.addEventListener('change', function() {
            if (this.checked) {
                availableLimitInput.disabled = true;
                calculateAvailableLimit();
            } else {
                availableLimitInput.disabled = false;
            }
        });

        cardLimitInput.addEventListener('input', function() {
            if (autoCalculateCheckbox.checked) {
                calculateAvailableLimit();
            } else {
                validateLimits();
            }
        });
            }
        }

        cardLimitInput.addEventListener('blur', validateLimits);
        availableLimitInput.addEventListener('blur', validateLimits);
    </script>
    @endpush
@endsection