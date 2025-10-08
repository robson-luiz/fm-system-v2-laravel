@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Permissão</h2>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a>
                <span>/</span>
                <span>Permissões</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Listar</h3>
            <div class="content-box-btn">
                @can('create-permission')
                    <a href="{{ route('permissions.create') }}" class="btn-success-md align-icon-btn">
                        <!-- Ícone plus-circle (Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Cadastrar</span>
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
                <a href="{{ route('permissions.index') }}" type="submit" class="btn-warning-md flex items-center space-x-1">
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
                                    @can('show-permission')
                                        <a href="{{ route('permissions.show', ['permission' => $permission->id]) }}"
                                            class="btn-primary-md align-icon-btn">
                                            <!-- Ícone eye (Heroicons) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>Visualizar</span>
                                        </a>
                                    @endcan

                                    @can('edit-permission')
                                        <a href="{{ route('permissions.edit', ['permission' => $permission->id]) }}"
                                            class="btn-warning-md table-md-hidden">
                                            <!-- Ícone pencil-square (Heroicons) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Editar</span>
                                        </a>
                                    @endcan

                                    @can('destroy-permission')
                                        <form id="delete-form-{{ $permission->id }}"
                                            action="{{ route('permissions.destroy', ['permission' => $permission->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('delete')

                                            <button type="button" onclick="confirmDelete({{ $permission->id }})"
                                                class="btn-danger-md table-md-hidden">
                                                <!-- Ícone trash (Heroicons) -->
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                <span>Apagar</span>
                                            </button>

                                        </form>
                                    @endcan
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

            <div class="mt-2 p-3">
                {{ $permissions->onEachSide(1)->links() }}
            </div>
        </div>

    </div>
@endsection
