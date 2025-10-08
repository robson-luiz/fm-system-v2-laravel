@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Configurações</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('profile.show') }}" class="breadcrumb-link">Perfil</a>
                <span>/</span>
                <span>Editar Senha</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        
        <x-alert />

        <!-- Layout Principal com Menu Lateral e Conteúdo -->
        <div class="profile-grid-container">
            
            <!-- Menu Lateral de Configurações -->
            <div class="profile-menu-container">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Configurações</h3>
                    </div>
                    <nav class="p-2 space-y-1">
                        @can('edit-profile')
                            <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span>Editar</span>
                            </a>
                        @endcan

                        @can('edit-password-profile')
                            <a href="{{ route('profile.edit_password') }}" class="profile-menu-item active">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span>Editar Senha</span>
                            </a>
                        @endcan

                        @can('edit-profile-image')
                            <a href="{{ route('profile.edit_image') }}" class="profile-menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                                <span>Editar Foto</span>
                            </a>
                        @endcan
                    </nav>
                </div>
            </div>

            <!-- Área de Conteúdo Principal -->
            <div class="profile-content-container">
                
                <!-- Seção Editar Senha -->
                <div class="profile-section">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Alterar Senha</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Mantenha sua conta segura com uma senha forte</p>
                                </div>
                                <a href="{{ route('profile.show') }}" class="btn-primary-md align-icon-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>Ver Perfil</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <form action="{{ route('profile.update_password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="space-y-6">
                                    <div class="form-group">
                                        <label for="current_password" class="form-label">Senha Atual *</label>
                                        <div class="relative">
                                            <input type="password" name="current_password" id="current_password" class="form-input pr-10"
                                                placeholder="Digite sua senha atual" required>
                                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2" onclick="togglePassword('current_password', this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="form-label">Nova Senha *</label>
                                        <div class="relative">
                                            <input type="password" name="password" id="password" class="form-input pr-10"
                                                placeholder="Senha com no mínimo 8 caracteres" value="{{ old('password') }}" required>
                                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2" onclick="togglePassword('password', this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Requisitos da senha -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 mt-3 text-sm" id="password-requirements">
                                            <div id="req-number" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Um número
                                            </div>
                                            <div id="req-uppercase" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Uma letra maiúscula
                                            </div>
                                            <div id="req-length" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Use de 8-50 caracteres
                                            </div>
                                            <div id="req-special" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Um símbolos: #%+:$@&
                                            </div>
                                            <div id="req-lowercase" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Uma letra minúscula
                                            </div>
                                            <div id="req-latin" class="text-gray-500 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Apenas alfabeto latino
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha *</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input pr-10"
                                                placeholder="Confirmar a nova senha" value="{{ old('password_confirmation') }}" required>
                                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2"
                                                onclick="togglePassword('password_confirmation', this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <button type="submit" class="btn-warning-md align-icon-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                            </svg>
                                            <span>Alterar Senha</span>
                                        </button>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">* Campo obrigatório</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

@endsection