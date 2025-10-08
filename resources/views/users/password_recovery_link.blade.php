@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Usuários</h2>
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
            <h3 class="content-box-title">Recuperar Senha</h3>
            <div class="content-box-btn">
            </div>
        </div>

        <x-alert />

        <div class="shadow-md rounded-xl bg-white/80 p-6 mb-12 text-center dark:bg-zinc-800 dark:text-zinc-100">

            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-6 h-20 w-20 text-green-700" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
            </svg>

            <h1 class="text-4xl font-bold mb-4 text-gray-800 dark:text-zinc-100">Link para o usuário recuperar a senha!</h1>

            <p class="text-lg text-gray-700 dark:text-zinc-100 mb-4">
                {{ $resetLink }}
            </p>

            <div class="table-actions-align">                

                <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn-warning-md">
                    Voltar ao Usuário
                </a>

            </div>
        </div>

    </div>
@endsection
