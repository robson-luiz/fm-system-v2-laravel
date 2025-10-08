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

        <form action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="form-label">Nome *</label>
                <input type="text" name="name" id="name" class="form-input"
                    placeholder="Nome completo do usuário" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">E-mail *</label>
                <input type="text" name="email" id="email" class="form-input"
                    placeholder="Melhor e-mail do usuário" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-4">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-input" placeholder="CPF do usuário"
                    value="{{ old('cpf', $user->cpf) }}">
            </div>

            <div class="mb-4">
                <label for="alias" class="form-label">Apelido</label>
                <input type="text" name="alias" id="alias" class="form-input" placeholder="Apelido do usuário"
                    value="{{ old('alias', $user->alias) }}">
            </div>

            <div class="mb-4">
                @can('edit-roles-user')
                    <label class="form-label">Papel: </label>
                    @forelse ($roles as $role)
                        @if ($role != 'Super Admin' || Auth::user()->hasRole('Super Admin'))
                            <input type="checkbox" name="roles[]" id="role_{{ Str::slug($role) }}" value="{{ $role }}"
                                {{ in_array($role, old('roles', $userRoles)) ? 'checked' : '' }}>
                            <label for="role_{{ Str::slug($role) }}" class="form-input-checkbox">{{ $role }}</label>
                        @endif
                    @empty
                        <p>Nenhum papel disponível.</p>
                    @endforelse
                @endcan
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
