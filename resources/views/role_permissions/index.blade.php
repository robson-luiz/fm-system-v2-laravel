@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Permissão do Papel</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <a href="{{ route('roles.index') }}" class="breadcrumb-link">Papéis</a>
                <span>/</span>
                <span>Permissões</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Listar - {{ $role->name }}</h3>
            <div class="content-box-btn">
                @can('index-role')
                    <a href="{{ route('roles.index') }}" class="btn-info-md align-icon-btn">
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

        <!-- Início Formulário de Pesquisa -->
        <form class="form-search">

            <input type="text" name="title" class="form-input" placeholder="Digite o título"
                value="{{ $title }}">

            <input type="text" name="name" class="form-input" placeholder="Digite o nome" value="{{ $name }}">

            <div class="flex gap-1">
                <button type="submit" class="btn-primary-md flex items-center space-x-1">
                    <!-- Ícone magnifying-glass (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Pesquisar</span>
                </button>
                <a href="{{ route('role-permissions.index', ['role' => $role->id]) }}" type="submit"
                    class="btn-warning-md flex items-center space-x-1">
                    <!-- Ícone trash (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Limpar</span>
                </a>
            </div>
        </form>
        <!-- Fim Formulário de Pesquisa -->

        <div class="table-container mt-6">
            <table class="table">
                <thead>
                    <tr class="table-row-header">
                        <th class="table-header">ID</th>
                        <th class="table-header">Título</th>
                        <th class="table-header hidden lg:table-cell">Nome</th>
                        <th class="table-header center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Imprimir os registros --}}
                    @forelse ($permissions as $permission)
                        <tr class="table-row-body">
                            <td class="table-body">{{ $permission->id }}</td>
                            <td class="table-body">{{ $permission->title }}</td>
                            <td class="table-body hidden lg:table-cell">{{ $permission->name }}</td>
                            <td class="table-actions">
                                <div class="table-actions-align">
                                    @if (in_array($permission->id, $rolePermissions ?? []))
                                        <a
                                            href="{{ route('role-permissions.update', ['role' => $role->id, 'permission' => $permission->id]) }}">
                                            <span style="color: #086;">Liberado</span>
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('role-permissions.update', ['role' => $role->id, 'permission' => $permission->id]) }}">
                                            <span style="color: #f00;">Bloqueado</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @empty
                        <div class="alert-warning">
                            Nenhum registro encontrado!
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
