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
            <h3 class="content-box-title">Cadastrar</h3>
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
            </div>
        </div>

        <x-alert />
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="mb-4">
                <label for="name" class="form-label">Nome *</label>
                <input type="text" name="name" id="name" class="form-input"
                    placeholder="Nome completo do usuário" value="{{ old('name') }}" required>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" name="email" id="email" class="form-input"
                    placeholder="Melhor e-mail do usuário" value="{{ old('email') }}" required>
            </div>

            <div class="mb-4">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-input" placeholder="CPF do usuário"
                    value="{{ old('cpf') }}">
            </div>

            <div class="mb-4">
                <label for="alias" class="form-label">Apelido</label>
                <input type="text" name="alias" id="alias" class="form-input" placeholder="Apelido do usuário"
                    value="{{ old('alias') }}">
            </div>

            <div class="mb-4">
                @can('edit-roles-user')
                    <label class="form-label">Papel: </label>
                    @forelse ($roles as $role)
                        @if ($role != 'Super Admin' || Auth::user()->hasRole('Super Admin'))
                            <input type="checkbox" name="roles[]" id="role_{{ Str::slug($role) }}" value="{{ $role }}"
                                {{ collect(old('roles'))->contains($role) ? 'checked' : '' }}>
                            <label for="role_{{ Str::slug($role) }}" class="form-input-checkbox">{{ $role }}</label>
                        @endif
                    @empty
                        <p>Nenhum papel disponível.</p>
                    @endforelse
                @endcan
            </div>

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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 mt-3 text-sm mb-4 " id="password-requirements">
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <span class="required-field">* Campo obrigatório</span>
            </div>

            <button type="submit" class="btn-success-md align-icon-btn">
                <!-- Ícone plus-circle (Heroicons) -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Cadastrar</span>
            </button>

        </form>

    </div>
@endsection
