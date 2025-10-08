@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Usuário</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('users.index') }}" class="breadcrumb-link">Usuários</a>
                <span>/</span>
                <span>Usuário</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Editar</h3>
            <div class="content-box-btn">
                @can('index-user')
                    <a href="{{ route('users.index') }}" class="btn-info-md align-icon-btn">
                        <!-- Ícone queue-list (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                        <span>Listar</span>
                    </a>
                @endcan

                @can('show-user')
                    <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn-primary-md align-icon-btn">
                        <!-- Ícone eye (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>Visualizar</span>
                    </a>
                @endcan
            </div>
        </div>

        <x-alert />

        <form action="{{ route('users.update_password', ['user' => $user->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">

                <div class="form-group">
                    <label for="password" class="form-label">Nova Senha *</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="form-input pr-10"
                            placeholder="Senha com no mínimo 8 caracteres" value="{{ old('password') }}" required>
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2"
                            onclick="togglePassword('password', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-input pr-10" placeholder="Confirmar a nova senha"
                            value="{{ old('password_confirmation') }}" required>
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2"
                            onclick="togglePassword('password_confirmation', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <span class="required-field">* Campo obrigatório</span>
            </div>

            <button type="submit" class="btn-warning-md align-icon-btn">
                <!-- Ícone pencil-square (Heroicons) -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                <span>Salvar</span>
            </button>

        </form>

    </div>
@endsection
