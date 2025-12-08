/**
 * Modal Inteligente de Verificação de Contas Vencidas
 * Sistema que verifica contas pendentes no login e pergunta "Essas contas já foram pagas?"
 * com atualização automática do status
 */

import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página do dashboard (evita executar em outras páginas)
    const isDashboardPage = window.location.pathname === '/dashboard' || 
                           window.location.pathname.includes('/dashboard');
    
    if (!isDashboardPage) {
        console.log('Modal de verificação: Não está na página do dashboard');
        return;
    }
    
    // Verificar se o modal deve ser exibido (não foi exibido nesta sessão)
    const modalShownInSession = sessionStorage.getItem('overdueModalShown');
    
    console.log('Modal de verificação: Dashboard detectado');
    console.log('Modal já exibido nesta sessão?', modalShownInSession ? 'Sim' : 'Não');
    
    if (!modalShownInSession) {
        console.log('Modal de verificação: Verificando contas vencidas...');
        checkAndShowOverdueModal();
    } else {
        console.log('Modal de verificação: Modal já foi exibido nesta sessão. Para testar novamente, execute no console: sessionStorage.clear()');
    }
});

/**
 * Verificar contas vencidas e exibir modal se necessário
 */
async function checkAndShowOverdueModal() {
    try {
        console.log('Modal de verificação: Fazendo requisição para /dashboard/overdue-accounts');
        
        const response = await fetch('/dashboard/overdue-accounts', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        console.log('Modal de verificação: Resposta recebida, status:', response.status);

        if (!response.ok) {
            throw new Error('Erro ao buscar contas vencidas');
        }

        const result = await response.json();
        
        console.log('Modal de verificação: Dados recebidos:', result);
        console.log('Modal de verificação: Total de contas vencidas:', result.data?.total_count || 0);
        
        if (result.success && result.data.total_count > 0) {
            console.log('Modal de verificação: Exibindo modal com', result.data.total_count, 'contas');
            showOverdueModal(result.data);
        } else {
            console.log('Modal de verificação: Nenhuma conta vencida encontrada');
        }
    } catch (error) {
        console.error('Modal de verificação: Erro ao verificar contas vencidas:', error);
    }
}

/**
 * Exibir modal com contas vencidas
 */
function showOverdueModal(data) {
    const { expenses, installments, total_count, total_amount } = data;
    
    // Construir HTML da lista de contas
    let accountsListHTML = '<div class="space-y-3 max-h-96 overflow-y-auto">';
    
    // Adicionar despesas
    expenses.forEach(account => {
        accountsListHTML += createAccountItem(account);
    });
    
    // Adicionar parcelas
    installments.forEach(account => {
        accountsListHTML += createAccountItem(account);
    });
    
    accountsListHTML += '</div>';
    
    // Detectar tema atual
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Exibir SweetAlert2 com a lista de contas
    Swal.fire({
        title: '🔔 Contas Vencidas Detectadas',
        html: `
            <div class="text-left">
                <p class="mb-4" style="color: ${isDarkMode ? '#d1d5db' : '#374151'}">
                    Detectamos <strong>${total_count}</strong> conta(s) vencida(s) no valor total de 
                    <strong style="color: ${isDarkMode ? '#f87171' : '#dc2626'}">R$ ${formatMoney(total_amount)}</strong>.
                </p>
                <p class="mb-4 font-semibold" style="color: ${isDarkMode ? '#d1d5db' : '#374151'}">
                    Essas contas já foram pagas?
                </p>
                ${accountsListHTML}
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: '✓ Marcar Todas como Pagas',
        denyButtonText: '⊘ Deixar Pendentes',
        cancelButtonText: '× Fechar',
        confirmButtonColor: '#22c55e',
        denyButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        width: '800px',
        background: isDarkMode ? '#1f2937' : '#ffffff',
        color: isDarkMode ? '#f3f4f6' : '#111827',
        customClass: {
            confirmButton: 'font-semibold',
            denyButton: 'font-semibold',
            cancelButton: 'font-semibold'
        },
        didOpen: () => {
            // Marcar modal como exibido
            sessionStorage.setItem('overdueModalShown', 'true');
            
            // Aplicar estilos dark mode no ícone do SweetAlert2
            if (isDarkMode) {
                const icon = document.querySelector('.swal2-icon.swal2-warning');
                if (icon) {
                    icon.style.borderColor = '#f59e0b';
                    icon.style.color = '#f59e0b';
                }
            }
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            // Usuário escolheu marcar todas como pagas
            await markAllAccountsAsPaid(expenses, installments);
        } else if (result.isDenied) {
            // Usuário escolheu deixar pendentes
            showKeepPendingAlert();
        }
    });
}

/**
 * Criar HTML para um item de conta
 */
function createAccountItem(account) {
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Classes e cores baseadas no tema e dias de atraso
    let borderColor, bgColor, badgeBg, badgeColor;
    
    if (account.days_overdue > 30) {
        borderColor = '#ef4444';
        bgColor = isDarkMode ? 'rgba(239, 68, 68, 0.2)' : '#fee2e2';
        badgeBg = isDarkMode ? '#7f1d1d' : '#fecaca';
        badgeColor = isDarkMode ? '#fca5a5' : '#991b1b';
    } else if (account.days_overdue > 7) {
        borderColor = '#f59e0b';
        bgColor = isDarkMode ? 'rgba(245, 158, 11, 0.2)' : '#fef3c7';
        badgeBg = isDarkMode ? '#78350f' : '#fde68a';
        badgeColor = isDarkMode ? '#fbbf24' : '#92400e';
    } else {
        borderColor = isDarkMode ? '#6b7280' : '#d1d5db';
        bgColor = isDarkMode ? '#374151' : '#f9fafb';
        badgeBg = isDarkMode ? '#4b5563' : '#e5e7eb';
        badgeColor = isDarkMode ? '#d1d5db' : '#1f2937';
    }
    
    const overdueLabel = account.days_overdue > 30 ? 'Crítico' :
                         account.days_overdue > 7 ? 'Atenção' :
                         'Pendente';
    
    const textColor = isDarkMode ? '#f3f4f6' : '#111827';
    const subtextColor = isDarkMode ? '#9ca3af' : '#6b7280';
    
    return `
        <div style="border-left: 4px solid ${borderColor}; background-color: ${bgColor}; padding: 12px; border-radius: 0 8px 8px 0; margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: ${textColor}; margin: 0 0 4px 0;">${account.description}</p>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.875rem; color: ${subtextColor};">
                        <span>Vencimento: ${account.formatted_date}</span>
                        <span>•</span>
                        <span style="font-weight: 500;">${account.formatted_amount}</span>
                        ${account.card_name ? `<span>•</span><span>${account.card_name}</span>` : ''}
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; align-items: end; gap: 4px;">
                    <span style="background-color: ${badgeBg}; color: ${badgeColor}; padding: 4px 8px; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">
                        ${overdueLabel}
                    </span>
                    <span style="font-size: 0.75rem; color: ${subtextColor};">
                        ${account.days_overdue} dia(s) atrás
                    </span>
                </div>
            </div>
        </div>
    `;
}

/**
 * Marcar todas as contas como pagas
 */
async function markAllAccountsAsPaid(expenses, installments) {
    // Extrair IDs
    const expenseIds = expenses.map(e => e.id);
    const installmentIds = installments.map(i => i.id);
    
    // Detectar tema
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Mostrar loading
    Swal.fire({
        title: 'Atualizando...',
        html: 'Marcando contas como pagas...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        background: isDarkMode ? '#1f2937' : '#ffffff',
        color: isDarkMode ? '#f3f4f6' : '#111827',
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch('/dashboard/mark-accounts-paid', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                expense_ids: expenseIds,
                installment_ids: installmentIds
            })
        });

        if (!response.ok) {
            throw new Error('Erro ao marcar contas como pagas');
        }

        const result = await response.json();
        
        if (result.success) {
            // Atualizar estatísticas do dashboard
            await updateDashboardStats();
            
            // Mostrar mensagem de sucesso
            const isDarkMode = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Sucesso!',
                html: `
                    <p style="color: ${isDarkMode ? '#d1d5db' : '#374151'}">
                        ${result.expenses_updated + result.installments_updated} conta(s) marcada(s) como paga(s)!
                    </p>
                    <p style="font-size: 0.875rem; color: ${isDarkMode ? '#9ca3af' : '#6b7280'}; margin-top: 8px;">
                        O dashboard foi atualizado automaticamente.
                    </p>
                `,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#22c55e',
                background: isDarkMode ? '#1f2937' : '#ffffff',
                color: isDarkMode ? '#f3f4f6' : '#111827'
            });
        } else {
            throw new Error(result.message || 'Erro desconhecido');
        }
    } catch (error) {
        console.error('Erro ao marcar contas como pagas:', error);
        
        const isDarkMode = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível marcar as contas como pagas. Tente novamente.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444',
            background: isDarkMode ? '#1f2937' : '#ffffff',
            color: isDarkMode ? '#f3f4f6' : '#111827'
        });
    }
}

/**
 * Mostrar alerta para contas que permanecerão pendentes
 */
function showKeepPendingAlert() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    Swal.fire({
        title: 'Atenção!',
        html: `
            <p style="color: ${isDarkMode ? '#d1d5db' : '#374151'}">
                As contas permanecerão pendentes.
            </p>
            <p style="color: ${isDarkMode ? '#f87171' : '#dc2626'}; font-weight: 600; margin-top: 8px;">
                ⚠️ Pague o mais rápido possível para evitar juros e multas!
            </p>
        `,
        icon: 'warning',
        confirmButtonText: 'Entendi',
        confirmButtonColor: '#f59e0b',
        background: isDarkMode ? '#1f2937' : '#ffffff',
        color: isDarkMode ? '#f3f4f6' : '#111827',
        didOpen: () => {
            if (isDarkMode) {
                const icon = document.querySelector('.swal2-icon.swal2-warning');
                if (icon) {
                    icon.style.borderColor = '#f59e0b';
                    icon.style.color = '#f59e0b';
                }
            }
        }
    });
}

/**
 * Atualizar estatísticas do dashboard após mudanças
 */
async function updateDashboardStats() {
    try {
        const response = await fetch('/dashboard/updated-stats', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Erro ao atualizar estatísticas');
        }

        const result = await response.json();
        
        if (result.success) {
            updateDashboardUI(result.data);
        }
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
    }
}

/**
 * Atualizar interface do dashboard com novos dados
 */
function updateDashboardUI(data) {
    const { incomes, expenses, creditCards, balance } = data;
    
    // Atualizar card de Receitas
    updateCardValue('incomes-monthly-received', incomes.monthly_received);
    updateCardValue('incomes-monthly-pending', incomes.monthly_pending);
    
    // Atualizar card de Despesas
    updateCardValue('expenses-monthly-paid', expenses.monthly_paid);
    updateCardValue('expenses-monthly-pending', expenses.monthly_pending);
    updateBadge('expenses-overdue-count', expenses.overdue_count);
    updateBadge('expenses-due-soon-count', expenses.due_soon_count);
    
    // Atualizar card de Saldo
    updateCardValue('balance-monthly-actual', balance.monthly_actual);
    
    // Recarregar página para atualizar gráficos (opcional)
    // setTimeout(() => location.reload(), 2000);
}

/**
 * Atualizar valor em um card
 */
function updateCardValue(elementId, value) {
    const element = document.querySelector(`[data-stat="${elementId}"]`);
    if (element) {
        element.textContent = 'R$ ' + formatMoney(value);
        
        // Adicionar animação de atualização
        element.classList.add('animate-pulse');
        setTimeout(() => element.classList.remove('animate-pulse'), 1000);
    }
}

/**
 * Atualizar badge com contador
 */
function updateBadge(elementId, count) {
    const element = document.querySelector(`[data-stat="${elementId}"]`);
    if (element) {
        element.textContent = count;
        
        // Adicionar animação de atualização
        element.classList.add('animate-pulse');
        setTimeout(() => element.classList.remove('animate-pulse'), 1000);
    }
}

/**
 * Formatar valor monetário
 */
function formatMoney(value) {
    return parseFloat(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Limpar flag do modal ao fazer logout ou navegar
 */
window.addEventListener('beforeunload', function() {
    // Manter o flag para não mostrar novamente na mesma sessão
    // sessionStorage.removeItem('overdueModalShown');
});
