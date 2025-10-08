@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Permissão</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('permissions.index') }}" class="breadcrumb-link">Permissões</a>
                <span>/</span>
                <span>Permissão</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Detalhes</h3>
            <div class="content-box-btn">
                @can('index-permission')
                    <a href="{{ route('permissions.index') }}" class="btn-info-md align-icon-btn">
                        <!-- Ícone queue-list (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                        <span>Listar</span>
                    </a>
                @endcan

                @can('edit-permission')
                    <a href="{{ route('permissions.edit', ['permission' => $permission->id]) }}" class="btn-warning-md align-icon-btn">
                        <!-- Ícone pencil-square (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Editar</span>
                    </a>
                @endcan

                @can('destroy-permission')
                    <form id="delete-form-{{ $permission->id }}" action="{{ route('permissions.destroy', ['permission' => $permission->id]) }}" method="POST">
                        @csrf
                        @method('delete')

                        <button type="button" onclick="confirmDelete({{ $permission->id }})"
                            class="btn-danger-md flex items-center space-x-1">
                            <!-- Ícone trash (Heroicons) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Apagar</span>
                        </button>

                    </form>
                @endcan
            </div>
        </div>

        <x-alert />
        <div class="detail-box">

            <div class="mb-1">
                <span class="title-detail-content">ID: </span>
                <span class="detail-content">{{ $permission->id }}</span>
            </div>

            <div class="mb-1">
                <span class="title-detail-content">Título: </span>
                <span class="detail-content">{{ $permission->title }}</span>
            </div>

            <div class="mb-1">
                <span class="title-detail-content">Nome: </span>
                <span class="detail-content">{{ $permission->name }}</span>
            </div>

            <div class="mb-1">
                <span class="title-detail-content">Cadastrado: </span>
                <span class="detail-content">{{ \Carbon\Carbon::parse($permission->created_at)->format('d/m/Y H:i:s') }}</span>
            </div>

            <div class="mb-1">
                <span class="title-detail-content">Editado: </span>
                <span class="detail-content">{{ \Carbon\Carbon::parse($permission->updated_at)->format('d/m/Y H:i:s') }}</span>
            </div>

        </div>

    </div>
@endsection
