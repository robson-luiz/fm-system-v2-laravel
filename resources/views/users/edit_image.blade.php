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

        <div class="mb-6 text-left">
            <div class="relative inline-block">
                @if ($user->image)
                    <img id="preview-image" src="{{ $user->image_url }}" alt="{{ $user->name }}"
                        class="w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-gray-200 dark:ring-gray-600">
                @else
                    <div id="preview-image"
                        class="w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-gray-600 dark:to-gray-700 
                                                                       rounded-full flex items-center justify-center shadow-lg ring-4 ring-gray-200 dark:ring-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-12 h-12 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('users.update_image', ['user' => $user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="image" class="form-label">Escolher Nova Foto *</label>
                <input type="file" name="image" id="image" accept="image/*" class="form-input"
                    onchange="previewImageUpload()" required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Formatos aceitos: JPG ou PNG. Tamanho máximo: 2MB.
                </p>
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
