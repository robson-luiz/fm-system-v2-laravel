@extends('layouts.admin')

@section('content')
    
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Perfil</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Perfil</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Meu Perfil</h3>
            <div class="content-box-btn">
                @can('edit-profile')
                    <a href="{{ route('profile.edit') }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone pencil-square (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Editar</span>
                    </a>
                @endcan
            </div>
        </div>

        <x-alert />

        <!-- Profile Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- 1ª Parte - Left Column - Profile Image, Name and Info -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Profile Image and Stats Section -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
                    
                    <!-- Profile Image Section -->
                    <div class="relative mb-6 flex justify-center">
                        <div class="w-28 h-28 bg-blue-200
                                    dark:bg-gray-900 rounded-full flex items-center justify-center
                                    relative shadow-lg ring-4 ring-white dark:ring-gray-700">

                            @if($user->image)
                                <img src="{{ $user->image_url }}"
                                    alt="{{ $user->name }}"
                                    class="w-28 h-28 rounded-full object-cover shadow-md">
                            @else
                                <!-- Default user icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-12 h-12 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            @endif

                            <!-- Edit image icon -->
                            @can('edit-profile')
                                <a href="{{ route('profile.edit_image') }}" class="absolute -top-2 -right-2 bg-blue-500 hover:bg-blue-600 text-white
                                                p-2 rounded-full shadow-lg transition-all duration-200 hover:scale-110
                                                border-2 border-white dark:border-gray-700">
                                    <!-- Camera icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                    </svg>
                                </a>
                            @endcan

                        </div>
                    </div>

                    <!-- User Name with Email Verification Badge -->
                    <div class="mb-4">
                        <div class="flex items-center justify-center space-x-2 flex-wrap mb-2">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $user->name }}</h2>
                            @if($user->email_verified_at)
                                <!-- Verified badge -->
                                <div class="flex items-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 mr-1">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.236 4.53L7.71 10.183a.75.75 0 0 0-1.32.75l2.065 3.645a.75.75 0 0 0 1.267.086l4.134-5.791Z" clip-rule="evenodd" />
                                    </svg>
                                    Verificado
                                </div>
                            @endif
                        </div>
                        @if($user->roles->isNotEmpty())
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->roles->first()->title }}</p>
                        @endif
                    </div>

                    <!-- Account Created -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 text-blue-800 dark:text-blue-200 px-4 py-3 rounded-xl border border-blue-200 dark:border-blue-700 shadow-sm">
                        <div class="text-sm font-medium">Membro desde</div>
                        <div class="text-base font-bold">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- About Me Section -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            Informações Pessoais
                        </h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                Apelido
                            </span>
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $user->alias ?? 'Não informado' }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                Email
                            </span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 truncate ml-2">{{ $user->email }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Z" />
                                </svg>
                                CPF
                            </span>
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $user->cpf_formatted ?? 'Não informado' }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2ª Parte - Right Column - Account Info and Activity -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Account Settings Quick Links -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Configurações da Conta
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @can('edit-profile')
                            <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Editar Perfil</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Atualize suas informações</p>
                                </div>
                            </a>
                        @endcan

                        @can('edit-password-profile')
                            <a href="{{ route('profile.edit_password') }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600 dark:text-green-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Alterar Senha</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Mantenha sua conta segura</p>
                                </div>
                            </a>
                        @endcan

                        @can('edit-profile')
                            <a href="{{ route('profile.edit_image') }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600 dark:text-purple-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Foto do Perfil</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Altere sua foto</p>
                                </div>
                            </a>
                        @endcan

                        <a href="{{ route('dashboard.index') }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                            <div class="flex-shrink-0 w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-orange-600 dark:text-orange-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Dashboard</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Visão geral financeira</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Account Security Info -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-green-600 dark:text-green-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        Segurança da Conta
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.236 4.53L7.71 10.183a.75.75 0 0 0-1.32.75l2.065 3.645a.75.75 0 0 0 1.267.086l4.134-5.791Z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">E-mail Verificado</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Seu e-mail foi verificado em {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 dark:text-gray-400 mt-0.5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Senha</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Recomendamos alterar sua senha periodicamente</p>
                                    @can('edit-password-profile')
                                        <a href="{{ route('profile.edit_password') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Alterar senha →</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
