@extends('layouts.login')

@section('content')
    <h1 class="title-login">Cadastrar Usuário</h1>

    <x-alert />

    <form class="mt-4" action="{{ route('register.store') }}" method="POST">
        @csrf
        @method('POST')

        <!-- Campo name -->
        <div class="form-group-login">
            <label for="name" class="form-label-login">Nome</label>
            <input type="text" name="name" id="name" placeholder="Digite o nome completo" class="form-input-login"
                value="{{ old('name') }}" required>
        </div>

        <!-- Campo e-mail -->
        <div class="form-group-login">
            <label for="email" class="form-label-login">E-mail</label>
            <input type="email" name="email" id="email" placeholder="Digite o seu melhor e-mail"
                class="form-input-login" value="{{ old('email') }}" required>
        </div>

        <!-- Campo senha -->
        <div class="form-group-login relative">
            <label for="password" class="form-label-login">Senha</label>
            <input type="password" name="password" id="password" placeholder="Senha com no mínimo 6 caracteres"
                class="form-input-login pr-10" value="{{ old('password') }}" required>

            <button type="button" class="absolute right-2 top-[38px]" onclick="togglePassword('password', this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>

        <!-- Requisitos da senha -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 mb-4 mt-2 text-sm" id="password-requirements">
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

        <!-- Campo confirmar senha -->
        <div class="form-group-login relative">
            <label for="password_confirmation" class="form-label-login">Confirmar Senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar a senha"
                class="form-input-login pr-10" value="{{ old('password_confirmation') }}" required>

            <button type="button" class="absolute right-2 top-[38px]"
                onclick="togglePassword('password_confirmation', this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>

        <div class="mt-4 mb-4">
            <label class="inline-flex items-start gap-2">
                <input type="checkbox" name="accept_terms" class="form-checkbox mt-1" required>
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Eu li e aceito os
                    <a href="https://celke.com.br/termos-de-uso" target="_blank"
                        class="no-underline text-blue-600 hover:text-blue-800">
                        Termos de Uso
                    </a> e a
                    <a href="https://celke.com.br/politica-de-privacidade" target="_blank"
                        class="no-underline text-blue-600 hover:text-blue-800">
                        Política de Privacidade
                    </a>.
                </span>
            </label>
        </div>

        <!-- Link para página de login -->
        <div class="btn-group-login">
            <a href="{{ route('login') }}" class="link-login">Login</a>
            <button type="submit" class="btn-primary-md">Cadastrar</button>
        </div>
    </form>
@endsection
