import './bootstrap';
import './chart-config';
import './money-mask';
import './overdue-verification-modal';
import './chart-dashboard';
import './cash-flow-charts';
import Swal from 'sweetalert2';
import axios from 'axios';

/**** Script para abrir/fechar o dropdown ****/
const dropdownButton = document.getElementById('userDropdownButton');
const dropdownContent = document.getElementById('dropdownContent');

dropdownButton.addEventListener('click', function () {
    const isOpen = dropdownContent.classList.contains('hidden');
    if (isOpen) {
        dropdownContent.classList.remove('hidden');
    } else {
        dropdownContent.classList.add('hidden');
    }
});

// Fechar o dropdown se clicar fora dele
window.addEventListener('click', function (event) {
    if (!dropdownButton.contains(event.target) && !dropdownContent.contains(event.target)) {
        dropdownContent.classList.add('hidden');
    }
});

/**** Apresentar e ocultar sidebar ****/
document.getElementById('toggleSidebar').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('sidebar-open');
});

document.getElementById('closeSidebar').addEventListener('click', function () {
    document.getElementById('sidebar').classList.remove('sidebar-open');
});

/**** Alterna entre tema claro e escuro ****/
document.addEventListener("DOMContentLoaded", function () {

    // Obter o elemento <html> para manipular a classe dark
    const htmlElement = document.documentElement;

    // Obter o id do botão tema claro e escuro
    const themeToggle = document.getElementById("themeToggle");

    // Obter o id do ícone escuro
    const iconMoon = document.getElementById("iconMoon");

    // Obter o id do ícone claro
    const iconSun = document.getElementById("iconSun");

    // Função para alternar os ícones claro e escuro
    function updateIcons() {
        if (htmlElement.classList.contains("dark")) {
            iconMoon.classList.remove("hidden");
            iconSun.classList.add("hidden");
        } else {
            iconMoon.classList.add("hidden");
            iconSun.classList.remove("hidden");
        }
    }

    // Aplicar o tema salvo no localStorage ou a preferência do sistema
    const isDarkMode = localStorage.theme === "dark" || // Se o localStorage.theme for "dark", ativa o modo escuro
        (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches);
    // Se NÃO houver um tema salvo no localStorage, verifica se o sistema está em dark mode

    htmlElement.classList.toggle("dark", isDarkMode);
    updateIcons(); // Atualiza os ícones na inicialização

    // Evento de clique para alternar o tema e os ícones
    themeToggle.addEventListener("click", function () {
        htmlElement.classList.toggle("dark");
        localStorage.theme = htmlElement.classList.contains("dark") ? "dark" : "light";
        updateIcons(); // Atualiza os ícones após alterar o tema
    });
});

// Função para apresentar o SweetAlert2 para confirmar a exclusão
window.confirmDelete = function (id) {
    Swal.fire({
        title: "Tem certeza?",
        text: "Essa ação não pode ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sim, excluir!",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Função para deletar wishlist
window.deleteWishlist = function(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Deseja remover este objetivo da wishlist?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/wishlist/${id}`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Mascara para o campo CPF
const cpfInput = document.getElementById("cpf");

function aplicarMascaraCPF(valor) {
    let value = valor.replace(/\D/g, ''); // Remove tudo que não é número
    if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos

    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

    return value;
}

if (cpfInput) {
    // Aplica a máscara no carregamento da página
    cpfInput.value = aplicarMascaraCPF(cpfInput.value);

    // Aplica a máscara enquanto digita
    cpfInput.addEventListener("input", function (e) {
        e.target.value = aplicarMascaraCPF(e.target.value);
    });
}

// Função para validação de senha
document.addEventListener("DOMContentLoaded", function () {
    // Função genérica para validar senha
    function setupPasswordValidation(passwordInputId, requirementsPrefix) {
        const passwordInput = document.getElementById(passwordInputId);
        if (!passwordInput) return;

        passwordInput.addEventListener("input", function () {
            const value = this.value;

            const requirements = [{
                id: `req-uppercase${requirementsPrefix}`,
                regex: /[A-Z]/ // Letra maiúscula
            },
            {
                id: `req-lowercase${requirementsPrefix}`,
                regex: /[a-z]/ // Letra minúscula
            },
            {
                id: `req-number${requirementsPrefix}`,
                regex: /[0-9]/ // Número
            },
            {
                id: `req-special${requirementsPrefix}`,
                test: val => /^[A-Za-z0-9#%+:$@&]*$/.test(val) && /[#%+:$@&]/.test(val)
                // Apenas símbolos permitidos e pelo menos um deles presente
            },
            {
                id: `req-length${requirementsPrefix}`,
                test: val => val.length >= 8 && val.length <= 50
                // Comprimento entre 8 e 50
            },
            {
                id: `req-latin${requirementsPrefix}`,
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
    }

    // Configurar validação para ambas as páginas
    setupPasswordValidation('password', ''); // Página edit_password.blade.php
    setupPasswordValidation('password_main', '-main'); // Seção na página edit.blade.php
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

/**** Função para preview de imagem ****/
window.previewImageUpload = function () {
    const input = document.getElementById('image');
    const preview = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            // Se o preview é uma imagem
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Se o preview é uma div (placeholder), substitui por imagem
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                img.id = 'preview-image';
                img.className = 'w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-gray-200 dark:ring-gray-600';
                preview.parentNode.replaceChild(img, preview);
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
};

/* ============================================
   SCRIPTS PARA DESPESAS (EXPENSES)
   ============================================ */

// Contexto: Página de criação/edição de despesas
if (document.getElementById('num_installments')) {
    
    // Calcular valor das parcelas em tempo real (Parcelas Iguais)
    const amountInput = document.getElementById('amount');
    const installmentsInput = document.getElementById('num_installments');
    const installmentInfo = document.getElementById('installment-info');
    const installmentCount = document.getElementById('installment-count');
    const installmentAmount = document.getElementById('installment-amount');
    const installmentTypeContainer = document.getElementById('installment-type-container');
    const installmentTypeEqual = document.getElementById('installment_type_equal');
    const installmentTypeCustom = document.getElementById('installment_type_custom');
    const customInstallmentsContainer = document.getElementById('custom-installments-container');
    const customInstallmentsFields = document.getElementById('custom-installments-fields');
    const dueDateInput = document.getElementById('due_date');

    function updateInstallmentInfo() {
        if (!amountInput || !installmentsInput || !installmentInfo) return;

        const amount = window.moneyToDecimal(amountInput.value) || 0;
        const installments = parseInt(installmentsInput.value) || 1;

        // Mostrar/ocultar opções de parcelamento
        if (installments > 1) {
            installmentInfo.classList.remove('hidden');
            installmentTypeContainer.classList.remove('hidden');
            installmentCount.textContent = installments;
            const perInstallment = amount / installments;
            installmentAmount.textContent = 'R$ ' + perInstallment.toFixed(2).replace('.', ',');
        } else {
            installmentInfo.classList.add('hidden');
            installmentTypeContainer.classList.add('hidden');
            customInstallmentsContainer.classList.add('hidden');
        }
    }

    // Gerar campos para parcelas personalizadas
    function generateCustomInstallmentFields(numInstallments) {
        const totalAmount = window.moneyToDecimal(amountInput.value) || 0;
        const baseAmount = (totalAmount / numInstallments).toFixed(2);
        const firstDueDate = dueDateInput.value;

        let html = '';
        
        for (let i = 1; i <= numInstallments; i++) {
            // Calcular data de vencimento sugerida (mês a mês)
            let suggestedDate = '';
            if (firstDueDate) {
                const date = new Date(firstDueDate);
                date.setMonth(date.getMonth() + (i - 1));
                suggestedDate = date.toISOString().split('T')[0];
            }

            html += `
                <div class="grid grid-cols-12 gap-3 items-start border-b border-gray-200 dark:border-gray-700 pb-3">
                    <div class="col-span-1 flex items-center justify-center">
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${i}</span>
                    </div>
                    <div class="col-span-5">
                        <label class="text-xs text-gray-600 dark:text-gray-400">Valor da Parcela ${i}</label>
                        <input type="text" 
                               name="custom_installments[${i-1}][amount]" 
                               class="form-input money-input custom-installment-amount" 
                               data-installment="${i}"
                               value="R$ ${parseFloat(baseAmount).toFixed(2).replace('.', ',')}"
                               required>
                    </div>
                    <div class="col-span-6">
                        <label class="text-xs text-gray-600 dark:text-gray-400">Data de Vencimento</label>
                        <input type="date" 
                               name="custom_installments[${i-1}][due_date]" 
                               class="form-input" 
                               value="${suggestedDate}"
                               required>
                    </div>
                </div>
            `;
        }

        customInstallmentsFields.innerHTML = html;

        // Aplicar máscara de dinheiro nos novos campos
        document.querySelectorAll('.custom-installment-amount').forEach(input => {
            function formatMoney(value) {
                value = value.replace(/\D/g, '');
                value = value.replace(/^0+/, '');

                if (value.length === 0) return '';
                if (value.length === 1) return 'R$ 0,0' + value;
                if (value.length === 2) return 'R$ 0,' + value;

                let integerPart = value.slice(0, -2);
                let decimalPart = value.slice(-2);
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                return 'R$ ' + integerPart + ',' + decimalPart;
            }

            input.addEventListener('input', (e) => {
                e.target.value = formatMoney(e.target.value);
                updateCustomInstallmentsSum();
            });
        });

        updateCustomInstallmentsSum();
    }

    // Atualizar soma das parcelas personalizadas
    function updateCustomInstallmentsSum() {
        const customAmounts = document.querySelectorAll('.custom-installment-amount');
        let sum = 0;

        customAmounts.forEach(input => {
            const value = window.moneyToDecimal(input.value) || 0;
            sum += value;
        });

        const totalAmount = window.moneyToDecimal(amountInput.value) || 0;
        const difference = sum - totalAmount;

        document.getElementById('installments-sum').textContent = 'R$ ' + sum.toFixed(2).replace('.', ',');
        document.getElementById('installments-total').textContent = 'R$ ' + totalAmount.toFixed(2).replace('.', ',');

        const differenceSpan = document.getElementById('installments-difference');
        
        if (Math.abs(difference) < 0.01) {
            differenceSpan.innerHTML = '<span class="text-green-600 dark:text-green-400">✓ Valores conferem!</span>';
        } else if (difference > 0) {
            differenceSpan.innerHTML = '<span class="text-red-600 dark:text-red-400">⚠ Excede em R$ ' + Math.abs(difference).toFixed(2).replace('.', ',') + '</span>';
        } else {
            differenceSpan.innerHTML = '<span class="text-orange-600 dark:text-orange-400">⚠ Faltam R$ ' + Math.abs(difference).toFixed(2).replace('.', ',') + '</span>';
        }
    }

    // Event listeners
    if (amountInput && installmentsInput) {
        amountInput.addEventListener('input', updateInstallmentInfo);
        installmentsInput.addEventListener('input', () => {
            updateInstallmentInfo();
            
            // Se tipo personalizado está selecionado, regenerar campos
            if (installmentTypeCustom && installmentTypeCustom.checked) {
                const numInstallments = parseInt(installmentsInput.value) || 1;
                if (numInstallments > 1) {
                    generateCustomInstallmentFields(numInstallments);
                }
            }
        });
        
        updateInstallmentInfo();
    }

    // Toggle entre parcelas iguais e personalizadas
    if (installmentTypeEqual && installmentTypeCustom) {
        installmentTypeEqual.addEventListener('change', function() {
            if (this.checked) {
                customInstallmentsContainer.classList.add('hidden');
                installmentInfo.classList.remove('hidden');
            }
        });

        installmentTypeCustom.addEventListener('change', function() {
            if (this.checked) {
                installmentInfo.classList.add('hidden');
                customInstallmentsContainer.classList.remove('hidden');
                
                const numInstallments = parseInt(installmentsInput.value) || 1;
                if (numInstallments > 1) {
                    generateCustomInstallmentFields(numInstallments);
                }
            }
        });
    }

    // Atualizar campos personalizados quando mudar o valor total
    if (amountInput) {
        amountInput.addEventListener('input', () => {
            if (installmentTypeCustom && installmentTypeCustom.checked) {
                updateCustomInstallmentsSum();
            }
        });
    }

    // Interceptar submit do formulário para converter valores monetários
    const expenseForm = document.getElementById('expense-form');
    if (expenseForm) {
        expenseForm.addEventListener('submit', function(e) {
            // Se parcelas personalizadas estão ativas
            if (installmentTypeCustom && installmentTypeCustom.checked) {
                const customAmounts = document.querySelectorAll('.custom-installment-amount');
                
                // Converter cada valor de "R$ 1.000,00" para "1000.00"
                customAmounts.forEach(input => {
                    const decimalValue = window.moneyToDecimal(input.value);
                    input.value = decimalValue.toFixed(2); // Formato decimal para backend
                });
            }
            
            // Também converter o valor principal se existir
            if (amountInput && amountInput.classList.contains('money-input')) {
                const decimalValue = window.moneyToDecimal(amountInput.value);
                amountInput.value = decimalValue.toFixed(2);
            }
        });
    }
}

// Contexto: Página de visualização de despesa (show)
if (window.location.pathname.includes('/expenses/') && !window.location.pathname.includes('/edit') && !window.location.pathname.includes('/create')) {
    
    // Função para confirmar deleção de despesa
    window.confirmDelete = function(expenseId) {
        Swal.fire({
            title: 'Confirmar Exclusão',
            text: 'Tem certeza que deseja excluir esta despesa? Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + expenseId).submit();
            }
        });
    };

    // Função para marcar despesa como paga
    window.markExpenseAsPaid = function() {
        Swal.fire({
            title: 'Marcar como Paga',
            html: `
                <div style="text-align: center; padding: 10px;">
                    <label for="payment_date" style="display: block; font-weight: 500; margin-bottom: 8px; color: inherit;">
                        Data do Pagamento
                    </label>
                    <input type="date" 
                           id="payment_date" 
                           class="swal2-input" 
                           value="${new Date().toISOString().split('T')[0]}" 
                           style="width: 100%; max-width: 300px; margin: 0 auto; display: block;" 
                           required>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Marcar como Paga',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
            preConfirm: () => {
                const paymentDate = document.getElementById('payment_date').value;
                if (!paymentDate) {
                    Swal.showValidationMessage('Por favor, selecione a data do pagamento');
                    return false;
                }
                return { payment_date: paymentDate };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                
                fetch(window.location.pathname + '/mark-paid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: data.error || 'Ocorreu um erro ao processar a solicitação.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Ocorreu um erro de comunicação.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    });
                });
            }
        });
    };

    // Função para marcar despesa como atrasada
    window.markExpenseAsOverdue = function() {
        Swal.fire({
            title: 'Não Conseguiu Pagar?',
            html: `
                <div style="text-align: center; padding: 10px;">
                    <label for="reason_not_paid" style="display: block; font-weight: 500; margin-bottom: 8px; color: inherit;">
                        Motivo (obrigatório)
                    </label>
                    <textarea id="reason_not_paid" 
                              class="swal2-textarea" 
                              placeholder="Descreva o motivo pelo qual não conseguiu pagar..." 
                              rows="4" 
                              style="width: 100%; max-width: 400px; margin: 0 auto; display: block; resize: vertical; min-height: 80px;"
                              required></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Marcar como Atrasada',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
            preConfirm: () => {
                const reason = document.getElementById('reason_not_paid').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Por favor, informe o motivo');
                    return false;
                }
                return { reason_not_paid: reason };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                
                fetch(window.location.pathname + '/mark-overdue', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Atualizado!',
                            text: data.message,
                            icon: 'info',
                            confirmButtonColor: '#3b82f6',
                            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: data.error || 'Ocorreu um erro ao processar a solicitação.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Ocorreu um erro de comunicação.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    });
                });
            }
        });
    };

    // Função para marcar parcela como paga (via AJAX)
    window.markInstallmentAsPaid = function(installmentId) {
        Swal.fire({
            title: 'Marcar Parcela como Paga',
            html: '<label class="block text-sm font-medium mb-2">Data de Pagamento:</label>' +
                  '<input type="date" id="payment_date" class="swal2-input" style="width: 90%; margin: 0;" value="' + new Date().toISOString().split('T')[0] + '">',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
            preConfirm: () => {
                const paymentDate = document.getElementById('payment_date').value;
                if (!paymentDate) {
                    Swal.showValidationMessage('Por favor, informe a data de pagamento');
                    return false;
                }
                return paymentDate;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                axios.post(`/expenses/installments/${installmentId}/mark-as-paid`, {
                    payment_date: result.value
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonColor: '#16a34a',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Erro!',
                        text: error.response?.data?.message || 'Erro ao atualizar parcela',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    });
                });
            }
        });
    };

    // Função para desfazer pagamento de parcela (via AJAX)
    window.markInstallmentAsUnpaid = function(installmentId) {
        Swal.fire({
            title: 'Desfazer Pagamento',
            text: 'Deseja desfazer o pagamento desta parcela?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, desfazer',
            cancelButtonText: 'Cancelar',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                axios.post(`/expenses/installments/${installmentId}/mark-as-unpaid`, {
                    reason_not_paid: null
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonColor: '#16a34a',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Erro!',
                        text: error.response?.data?.message || 'Erro ao atualizar parcela',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                    });
                });
            }
        });
    };
}

/**** Funções para Configurações 2FA ****/

// Carregar estatísticas de 2FA
function loadStatistics() {
    const statisticsRoute = document.querySelector('meta[name="two-factor-statistics-route"]');
    if (!statisticsRoute) return;
    
    fetch(statisticsRoute.getAttribute('content'))
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('statistics-content');
            if (content) {
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">${data.total_users || 0}</div>
                            <div class="text-sm text-gray-500">Total de Usuários</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-xl font-semibold text-green-600 dark:text-green-400">${data.users_with_2fa || 0}</div>
                                <div class="text-xs text-gray-500">Com 2FA</div>
                            </div>
                            <div>
                                <div class="text-xl font-semibold text-purple-600 dark:text-purple-400">${data.users_email_2fa || 0}</div>
                                <div class="text-xs text-gray-500">Via Email</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-xl font-semibold text-orange-600 dark:text-orange-400">${data.users_sms_2fa || 0}</div>
                                <div class="text-xs text-gray-500">Via SMS</div>
                            </div>
                            <div>
                                <div class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">${data.codes_sent_today || 0}</div>
                                <div class="text-xs text-gray-500">Códigos Hoje</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
            const content = document.getElementById('statistics-content');
            if (content) {
                content.innerHTML = '<div class="text-red-500 text-center">Erro ao carregar estatísticas</div>';
            }
        });
}

// Atualizar estatísticas de 2FA
function refreshStatistics() {
    const btn = document.getElementById('refresh-stats-btn');
    if (!btn) return;
    
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Atualizando...';
    
    // Mostrar loading nas estatísticas
    const content = document.getElementById('statistics-content');
    if (content) {
        content.innerHTML = `
            <div class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-gray-500 mt-2">Atualizando...</p>
            </div>
        `;
    }
    
    loadStatistics();
    
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        Swal.fire({
            title: 'Atualizado!',
            text: 'Estatísticas atualizadas com sucesso.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1000);
}

// Testar provedor SMS
function testProvider() {
    const testProviderRoute = document.querySelector('meta[name="two-factor-test-provider-route"]');
    
    // Se não há rota (página 2FA), redirecionar para Email/SMS
    if (!testProviderRoute) {
        Swal.fire({
            title: 'Configure SMS',
            text: 'Configure o provedor SMS na página Email e SMS.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ir para Email e SMS',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/admin/email-sms';
            }
        });
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Testando...';
    
    fetch(testProviderRoute.getAttribute('content'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            title: data.success ? 'Sucesso!' : 'Erro!',
            text: data.message,
            icon: data.success ? 'success' : 'error',
            confirmButtonColor: data.success ? '#10b981' : '#ef4444'
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Erro!',
            text: 'Erro de comunicação.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Testar SMS (2FA)
function testSms() {
    const testSmsRoute = document.querySelector('meta[name="two-factor-test-sms-route"]');
    
    // Se não há rota (página 2FA), redirecionar para Email/SMS
    if (!testSmsRoute) {
        Swal.fire({
            title: 'Configure SMS',
            text: 'Configure e teste SMS na página Email e SMS.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ir para Email e SMS',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/admin/email-sms';
            }
        });
        return;
    }
    
    const phone = document.getElementById('test-phone');
    if (!phone || !phone.value) {
        Swal.fire({
            title: 'Atenção!',
            text: 'Digite um número de telefone para teste.',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Enviando...';
    
    fetch(testSmsRoute.getAttribute('content'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_phone: phone.value })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            title: data.success ? 'SMS Enviado!' : 'Erro!',
            text: data.message,
            icon: data.success ? 'success' : 'error',
            confirmButtonColor: data.success ? '#10b981' : '#ef4444'
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Erro!',
            text: 'Erro de comunicação.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

/**** Funções para Configurações Email e SMS ****/

// Testar configuração de Email
function testEmail() {
    const testEmailRoute = document.querySelector('meta[name="email-sms-test-email-route"]');
    if (!testEmailRoute) return;
    
    const testEmailInput = document.querySelector('input[name="test_email"]');
    if (!testEmailInput || !testEmailInput.value) {
        Swal.fire({
            title: 'Atenção!',
            text: 'Digite um email para teste.',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Enviando...';
    
    fetch(testEmailRoute.getAttribute('content'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_email: testEmailInput.value })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            title: data.success ? 'Email Enviado!' : 'Erro!',
            text: data.message,
            icon: data.success ? 'success' : 'error',
            confirmButtonColor: data.success ? '#10b981' : '#ef4444'
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Erro!',
            text: 'Erro de comunicação.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Testar SMS (Email/SMS Settings)
function testSmsEmailSettings() {
    const testSmsRoute = document.querySelector('meta[name="email-sms-test-sms-route"]');
    if (!testSmsRoute) return;
    
    const testPhoneInput = document.querySelector('input[name="test_phone"]');
    if (!testPhoneInput || !testPhoneInput.value) {
        Swal.fire({
            title: 'Atenção!',
            text: 'Digite um número de telefone para teste.',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Enviando...';
    
    fetch(testSmsRoute.getAttribute('content'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_phone: testPhoneInput.value })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            title: data.success ? 'SMS Enviado!' : 'Erro!',
            text: data.message,
            icon: data.success ? 'success' : 'error',
            confirmButtonColor: data.success ? '#10b981' : '#ef4444'
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Erro!',
            text: 'Erro de comunicação.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Configurar mudança de provedor SMS (2FA)
function setupSmsProviderChange() {
    const smsProvider = document.getElementById('sms-provider');
    if (smsProvider) {
        smsProvider.addEventListener('change', function() {
            const provider = this.value;
            
            // Esconder todas as configurações
            document.querySelectorAll('.provider-config').forEach(config => {
                config.style.display = 'none';
            });
            
            // Mostrar configuração do provedor selecionado
            if (provider) {
                const configDiv = document.getElementById('config-' + provider);
                if (configDiv) {
                    configDiv.style.display = 'block';
                }
            }
        });
    }
}

// Inicializar funcionalidades das páginas de configuração
document.addEventListener('DOMContentLoaded', function() {
    // Carregar estatísticas se estiver na página 2FA
    if (document.getElementById('statistics-content')) {
        loadStatistics();
    }
    
    // Configurar mudança de provedor SMS
    setupSmsProviderChange();
});

// Disponibilizar funções globalmente
window.loadStatistics = loadStatistics;
window.refreshStatistics = refreshStatistics;
window.testProvider = testProvider;
window.testSms = testSms;
window.testEmail = testEmail;
window.testSmsEmailSettings = testSmsEmailSettings;

/**** Funcionalidades para SMS Customizado ****/

// Contadores para garantir IDs únicos - inicializados dinamicamente
let headerCounter = 0;
let fieldCounter = 0;
let successCounter = 0;

// Inicializar contadores baseado nos elementos existentes na página
function initializeSmsCounters() {
    // Contar headers existentes
    const headerPairs = document.querySelectorAll('#custom-headers-container .header-pair');
    headerCounter = headerPairs.length;
    
    // Contar campos existentes
    const fieldPairs = document.querySelectorAll('#custom-fields-container .field-pair');
    fieldCounter = fieldPairs.length;
    
    // Contar indicadores existentes
    const successPairs = document.querySelectorAll('#custom-success-container .success-pair');
    successCounter = successPairs.length;
}

// Adicionar novo par de header
function addHeaderPair() {
    const container = document.getElementById('custom-headers-container');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = 'header-pair flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="custom_sms_headers[${headerCounter}][key]" 
               class="form-input flex-1" placeholder="Chave (Ex: Authorization)">
        <input type="text" name="custom_sms_headers[${headerCounter}][value]" 
               class="form-input flex-1" placeholder="Valor (Ex: Bearer TOKEN)">
        <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeHeaderPair(this)">×</button>
    `;
    container.appendChild(div);
    headerCounter++;
}

// Remover par de header
function removeHeaderPair(button) {
    const headerPair = button.closest('.header-pair');
    if (headerPair) {
        headerPair.remove();
    }
}

// Adicionar novo par de campo
function addFieldPair() {
    const container = document.getElementById('custom-fields-container');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = 'field-pair flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="custom_sms_additional_fields[${fieldCounter}][key]" 
               class="form-input flex-1" placeholder="Campo (Ex: from, api_key)">
        <input type="text" name="custom_sms_additional_fields[${fieldCounter}][value]" 
               class="form-input flex-1" placeholder="Valor (Ex: FM System)">
        <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeFieldPair(this)">×</button>
    `;
    container.appendChild(div);
    fieldCounter++;
}

// Remover par de campo
function removeFieldPair(button) {
    const fieldPair = button.closest('.field-pair');
    if (fieldPair) {
        fieldPair.remove();
    }
}

// Adicionar novo par de indicador de sucesso
function addSuccessPair() {
    const container = document.getElementById('custom-success-container');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = 'success-pair flex gap-2 mb-2';
    div.innerHTML = `
        <select name="custom_sms_success_indicators[${successCounter}][key]" class="form-select flex-1">
            <option value="">Selecione...</option>
            <option value="status_code">Status Code</option>
            <option value="status">Campo "status"</option>
            <option value="success">Campo "success"</option>
            <option value="response_contains">Resposta contém</option>
            <option value="error">Campo "error"</option>
        </select>
        <input type="text" name="custom_sms_success_indicators[${successCounter}][value]" 
               class="form-input flex-1" placeholder="Valor esperado (Ex: 200, success, true)">
        <button type="button" class="bg-red-500 hover:bg-red-600 text-white text-sm px-2 py-1 rounded" onclick="removeSuccessPair(this)">×</button>
    `;
    container.appendChild(div);
    successCounter++;
}

// Remover par de indicador de sucesso
function removeSuccessPair(button) {
    const successPair = button.closest('.success-pair');
    if (successPair) {
        successPair.remove();
    }
}

// Configurar funcionalidades específicas da página de Email/SMS
function setupEmailSmsPage() {
    // Inicializar contadores se estiver na página de configuração SMS
    if (document.getElementById('custom-headers-container')) {
        initializeSmsCounters();
    }
}

// Atualizar a inicialização existente
document.addEventListener('DOMContentLoaded', function() {
    // Carregar estatísticas se estiver na página 2FA
    if (document.getElementById('statistics-content')) {
        loadStatistics();
    }
    
    // Configurar mudança de provedor SMS
    setupSmsProviderChange();
    
    // Configurar página Email/SMS
    setupEmailSmsPage();
});

// Disponibilizar funções SMS customizado globalmente
window.addHeaderPair = addHeaderPair;
window.removeHeaderPair = removeHeaderPair;
window.addFieldPair = addFieldPair;
window.removeFieldPair = removeFieldPair;
window.addSuccessPair = addSuccessPair;
window.removeSuccessPair = removeSuccessPair;
