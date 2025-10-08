@extends('layouts.login')

@section('content')
    <h1 class="title-login">Nova Senha</h1>

    <x-alert />

    <form class="mt-4" action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('POST')

        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Campo e-mail -->
        <div class="form-group-login">
            <label for="email" class="form-label-login">E-mail</label>
            <input type="email" name="email" id="email" placeholder="Digite o e-mail cadastrado"
                class="form-input-login" value="{{ old('email') }}" required>
        </div>

        <!-- Campo senha -->
        <div class="form-group-login relative">
            <label for="password" class="form-label-login">Senha</label>
            <input type="password" name="password" id="password" placeholder="Digite a nova senha"
                class="form-input-login pr-10" value="{{ old('password') }}" required>

            <button type="button" class="absolute right-2 top-[38px]" onclick="togglePassword('password', this)">
                <!-- Ícone olho aberto (inicial) -->
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

        <!-- Link para página de login -->
        <div class="btn-group-login">
            <a href="{{ route('login') }}" class="link-login">Login</a>
            <button type="submit" class="btn-primary-md">Atualizar</button>
        </div>

    </form>
@endsection
