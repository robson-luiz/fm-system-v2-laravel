import './bootstrap';

// Apresenta os requisitos de senha
document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password");

    if (!passwordInput) return; // Evita erro se o campo não existir

    passwordInput.addEventListener("input", function () {
        const value = this.value;

        const requirements = [{
            id: 'req-uppercase',
            regex: /[A-Z]/ // Letra maiúscula
        },
        {
            id: 'req-lowercase',
            regex: /[a-z]/ // Letra minúscula
        },
        {
            id: 'req-number',
            regex: /[0-9]/ // Número
        },
        {
            id: 'req-special',
            test: val => /^[A-Za-z0-9#%+:$@&]*$/.test(val) && /[#%+:$@&]/.test(val)
            // Apenas símbolos permitidos e pelo menos um deles presente
        },
        {
            id: 'req-length',
            test: val => val.length >= 8 && val.length <= 50
            // Comprimento entre 8 e 50
        },
        {
            id: 'req-latin',
            test: val => /^[A-Za-z0-9#%+:$@&]*$/.test(val)
            // Apenas alfabeto latino e símbolos permitidos
        }
        ];

        requirements.forEach(req => {
            const element = document.getElementById(req.id);
            if (!element) return;

            const passed = req.regex ? req.regex.test(value) : req.test(value);

            const dot = element.querySelector('span');
            element.classList.toggle('text-green-600', passed);
            element.classList.toggle('text-gray-500', !passed);
            dot.classList.toggle('bg-green-500', passed);
            dot.classList.toggle('bg-gray-400', !passed);
        });
    });
});

// Função para alternar visibilidade da senha
document.addEventListener("DOMContentLoaded", function () {
    window.togglePassword = function (fieldId, btn) {
        const input = document.getElementById(fieldId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        btn.innerHTML = isPassword
            ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.005-3.368M9.88 9.88a3 3 0 104.24 4.24M6.1 6.1l11.8 11.8" />
               </svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
               </svg>`;
    };
});