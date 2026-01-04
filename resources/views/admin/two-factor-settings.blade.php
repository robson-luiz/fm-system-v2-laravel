@extends('layouts.admin')

@section('content')
    <!-- Meta tags para rotas JavaScript -->
    <meta name="two-factor-statistics-route" content="{{ route('admin.two-factor.statistics') }}">

    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Configurações</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Autenticação 2FA</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Autenticação de Duas Etapas (2FA)</h3>
            <div class="content-box-btn">
                <button type="button" onclick="refreshStatistics()" class="btn-info-md align-icon-btn" id="refresh-stats-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>Atualizar Estatísticas</span>
                </button>
            </div>
        </div>

        <x-alert />

        <!-- Configurações Gerais -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal - Configurações -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Configurações Gerais -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configurações Gerais
                        </h4>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.two-factor.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="mb-4">
                                    <label class="form-label flex items-center">
                                        <input type="checkbox" name="enabled" value="1" class="form-checkbox mr-2" {{ $settings->enabled ? 'checked' : '' }}>
                                        Habilitar 2FA no Sistema
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ativa a autenticação de duas etapas para o sistema</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label flex items-center">
                                        <input type="checkbox" name="force_for_admins" value="1" class="form-checkbox mr-2" {{ $settings->force_for_admins ? 'checked' : '' }}>
                                        Obrigatório para Administradores
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Força 2FA para usuários com perfil de administrador</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="mb-4">
                                    <label class="form-label">Método Padrão</label>
                                    <select name="default_method" class="form-select" required>
                                        @foreach($availableProviders as $key => $provider)
                                            <option value="{{ $key }}" 
                                                    {{ $settings->default_method === $key ? 'selected' : '' }}
                                                    {{ !$provider['available'] ? 'disabled' : '' }}>
                                                {{ $provider['name'] }}
                                                @if(!$provider['available'])
                                                    ({{ $provider['description'] }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Método padrão para novos usuários
                                        @if(!$smsAvailable)
                                            <br><span class="text-amber-600">⚠️ Configure SMS em <a href="{{ route('admin.email-sms.index') }}" class="underline">Email e SMS</a></span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label flex items-center">
                                        <input type="checkbox" name="allow_user_choice" value="1" class="form-checkbox mr-2" {{ $settings->allow_user_choice ? 'checked' : '' }}>
                                        Permitir Escolha do Usuário
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Usuários podem escolher entre Email ou SMS</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="mb-4">
                                    <label class="form-label">Tempo de Expiração (minutos)</label>
                                    <input type="number" name="code_expiry_minutes" class="form-input" 
                                           value="{{ $settings->code_expiry_minutes }}" min="1" max="60" required>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tempo para o código expirar (1-60 minutos)</p>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Máximo de Tentativas</label>
                                    <input type="number" name="max_attempts" class="form-input" 
                                           value="{{ $settings->max_attempts }}" min="1" max="10" required>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tentativas antes de bloquear (1-10)</p>
                                </div>
                            </div>

                            <button type="submit" class="btn-success-md align-icon-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Salvar Configurações</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Status dos Provedores -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Status dos Provedores
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($availableProviders as $key => $provider)
                                <div class="flex items-center justify-between p-4 rounded-lg border {{ $provider['available'] ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20' }}">
                                    <div class="flex items-center">
                                        @if($provider['available'])
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        <div>
                                            <h6 class="font-semibold {{ $provider['available'] ? 'text-green-900 dark:text-green-100' : 'text-amber-900 dark:text-amber-100' }}">
                                                {{ $provider['name'] }}
                                            </h6>
                                            <p class="text-sm {{ $provider['available'] ? 'text-green-700 dark:text-green-300' : 'text-amber-700 dark:text-amber-300' }}">
                                                {{ $provider['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                    @if(!$provider['available'] && $key === 'sms')
                                        <a href="{{ route('admin.email-sms.index') }}" class="btn-primary-sm">
                                            Configurar
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h6 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Configuração de Provedores</h6>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        Configure provedores de Email e SMS na página 
                                        <a href="{{ route('admin.email-sms.index') }}" class="underline font-medium">Email e SMS</a>.
                                        Aqui você apenas define as políticas de uso do 2FA.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral - Estatísticas e Status -->
            <div class="space-y-6">
                <!-- Estatísticas -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Estatísticas de Uso
                        </h4>
                    </div>
                    <div class="p-6" id="statistics-content">
                        <div class="text-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-500 mt-2">Carregando...</p>
                        </div>
                    </div>
                </div>

                <!-- Status do Sistema -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            Status do Sistema
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">2FA Habilitado</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $settings->enabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $settings->enabled ? 'Sim' : 'Não' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Método Padrão</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ ucfirst($settings->default_method) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Obrigatório para Admins</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $settings->force_for_admins ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $settings->force_for_admins ? 'Sim' : 'Não' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 dark:text-gray-400">Provedor SMS</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $settings->sms_provider ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $settings->sms_provider ? ucfirst($settings->sms_provider) : 'Não configurado' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection