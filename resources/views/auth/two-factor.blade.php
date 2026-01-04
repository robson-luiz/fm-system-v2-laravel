@extends('layouts.login')

@section('title', 'Verificação em Duas Etapas')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-600 mb-4">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Verificação de Segurança
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if($method === 'sms')
                    Código enviado via SMS para <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $destination_masked }}</span>
                @else
                    Código enviado para <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $destination_masked }}</span>
                @endif
            </p>
        </div>

        <!-- Alertas -->
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Formulário -->
        <div class="bg-white dark:bg-gray-800 py-8 px-6 shadow-lg rounded-lg">
            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Código de Verificação
                    </label>
                    <input id="code" 
                           name="code" 
                           type="text" 
                           maxlength="6" 
                           pattern="[A-Za-z0-9]{6}"
                           autocomplete="one-time-code"
                           required 
                           autofocus
                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-xl font-mono tracking-widest bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           placeholder="XXXXXX"
                           value="{{ old('code') }}">
                    @error('code')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-105"
                        id="verify-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verificar Código
                </button>
            </form>

            <!-- Ações secundárias -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between text-sm">
                    <form method="POST" action="{{ route('two-factor.resend') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center"
                                id="resend-btn">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reenviar código
                        </button>
                    </form>
                    
                    <a href="{{ route('logout') }}" 
                       class="text-gray-600 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sair
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações de ajuda -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Não recebeu o código? Verifique sua caixa de spam ou aguarde alguns minutos.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    const verifyBtn = document.getElementById('verify-btn');
    const resendBtn = document.getElementById('resend-btn');
    
    // Auto-submit quando o código tiver 6 caracteres
    codeInput.addEventListener('input', function() {
        // Permitir apenas letras e números
        this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        
        // Auto-submit quando tiver 6 caracteres
        if (this.value.length === 6) {
            setTimeout(() => {
                verifyBtn.click();
            }, 500);
        }
    });
    
    // Cooldown para reenvio
    let resendCooldown = false;
    resendBtn.addEventListener('click', function(e) {
        if (resendCooldown) {
            e.preventDefault();
            return;
        }
        
        resendCooldown = true;
        const originalText = this.innerHTML;
        
        // Mostrar loading
        this.innerHTML = '<svg class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Enviando...';
        this.disabled = true;
        
        // Reativar após 30 segundos
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
            resendCooldown = false;
        }, 30000);
    });
    
    // Foco automático no input
    codeInput.focus();
});
</script>
@endsection