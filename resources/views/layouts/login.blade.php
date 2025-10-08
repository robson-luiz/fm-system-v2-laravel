<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FM System</title>

    <script>
        // Executar logo no início, antes de carregar o CSS e evitar o piscar na tela
        (function() {

            // Verificar se o usuário já definiu um tema na localStorage
            const theme = localStorage.getItem('theme');

            // Verificar se o sistema do usuário está configurado para o tema escuro
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Se o usuário escolheu o tema 'dark' ou se não há tema definido e o sistema prefere o modo escuro, aplica o tema escuro
            if (theme === 'dark' || (!theme && prefersDark)) {
                // Adicionar a classe 'dark' ao elemento raiz (html), ativando o modo escuro no site
                document.documentElement.classList.add('dark');
            } else {
                // Caso contrário, remove a classe 'dark' e aplica o tema claro
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app_auth.css', 'resources/js/app_auth.js'])
</head>

<body class="bg-login">

    {{-- Exibir o loading --}}
    <div id="loadingScreen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 dark:bg-gray-900/30">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
    </div>

    <div class="card-login">
        <div class="logo-wrapper-login">
            <a href="#">
                <img src="{{ asset('images/logo/fm_system.png') }}" alt="Logo" class="logo-login">
            </a>
        </div>

        @yield('content')

    </div>

    <script>
        window.addEventListener('load', function() {
            const loading = document.getElementById('loadingScreen');
            if (loading) {
                loading.style.display = 'none';
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const loading = document.getElementById('loadingScreen');

            if (form && loading) {
                form.addEventListener('submit', function() {
                    loading.style.display = 'flex'; 
                });
            }
        });
    </script>

</body>

</html>
