/**
 * Máscara de dinheiro brasileiro (R$ 1.234,56)
 */

// Função para formatar valor como dinheiro
function formatMoney(value) {
    // Remove tudo que não é número
    value = value.replace(/\D/g, '');
    
    // Converte para centavos
    value = (parseInt(value) / 100).toFixed(2);
    
    // Formata para o padrão brasileiro
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+\,)/g, '$1.');
    
    return value;
}

// Função para converter de formato brasileiro para decimal
function moneyToDecimal(value) {
    // Remove R$, espaços e pontos de milhar
    value = value.replace(/R\$\s?/g, '');
    value = value.replace(/\./g, '');
    // Substitui vírgula por ponto
    value = value.replace(',', '.');
    
    return parseFloat(value) || 0;
}

// Aplicar máscara em campos com a classe 'money-input' ou 'money-mask'
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('.money-input, .money-mask');
    
    moneyInputs.forEach(function(input) {
        // Formatar valor inicial se existir
        if (input.value) {
            let initialValue = input.value.replace(/\D/g, '');
            if (initialValue) {
                input.value = formatMoney(initialValue);
            }
        }
        
        // Aplicar máscara ao digitar
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            e.target.value = formatMoney(value);
        });
        
        // Converter para decimal antes de submeter o formulário
        input.form?.addEventListener('submit', function(e) {
                // Remover qualquer campo hidden anterior com o mesmo name
                const oldHidden = this.querySelector('input[type="hidden"][name="' + input.name + '"]');
                if (oldHidden) {
                    oldHidden.remove();
                }

                // Sempre garantir que o valor do input está formatado corretamente antes de converter
                input.value = formatMoney(input.value.replace(/\D/g, ''));

                // Criar campo hidden com valor decimal
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = moneyToDecimal(input.value);

                // Remover o name do input visível para não enviar
                input.removeAttribute('name');

                // Adicionar o hidden input ao form
                this.appendChild(hiddenInput);
        });
    });
});

// Exportar funções para uso global
window.formatMoney = formatMoney;
window.moneyToDecimal = moneyToDecimal;

