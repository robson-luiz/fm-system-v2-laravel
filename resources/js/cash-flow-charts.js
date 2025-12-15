// cash-flow-charts.js - Gráficos de Análise de Fluxo de Caixa

let historicalChart = null;
let projectionsChart = null;

// Configuração de cores baseada no tema
function getChartColors() {
    const isDark = document.documentElement.classList.contains('dark');
    
    return {
        income: isDark ? '#10b981' : '#059669',
        expense: isDark ? '#ef4444' : '#dc2626',
        balance: isDark ? '#3b82f6' : '#2563eb',
        grid: isDark ? '#374151' : '#e5e7eb',
        text: isDark ? '#d1d5db' : '#374151',
        projected: isDark ? '#8b5cf6' : '#7c3aed',
    };
}

// Formatar moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Carregar dados do servidor
async function loadCashFlowData() {
    const months = document.getElementById('periodFilter').value;
    
    try {
        const response = await fetch(`/cash-flow/data?months=${months}`);
        const data = await response.json();
        
        console.log('Cash Flow Data:', data);
        
        // Atualizar cards de resumo anual
        updateYearlySummary(data.yearly_flow);
        
        // Atualizar tendências
        updateTrends(data.trends);
        
        // Criar gráficos
        createHistoricalChart(data.monthly_flow);
        createProjectionsChart(data.projections);
        
        // Atualizar tabela
        updateDataTable(data.monthly_flow);
        
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados do fluxo de caixa.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

// Atualizar cards de resumo anual
function updateYearlySummary(yearlyData) {
    document.getElementById('yearlyIncome').textContent = formatCurrency(yearlyData.total_income);
    document.getElementById('avgMonthlyIncome').textContent = formatCurrency(yearlyData.avg_monthly_income);
    
    document.getElementById('yearlyExpense').textContent = formatCurrency(yearlyData.total_expense);
    document.getElementById('avgMonthlyExpense').textContent = formatCurrency(yearlyData.avg_monthly_expense);
    
    const balanceElement = document.getElementById('yearlyBalance');
    const balanceStatusElement = document.getElementById('balanceStatus');
    
    balanceElement.textContent = formatCurrency(yearlyData.balance);
    
    if (yearlyData.balance >= 0) {
        balanceElement.className = 'text-2xl font-bold mt-2 text-green-600 dark:text-green-400';
        balanceStatusElement.textContent = '✅ Superávit';
        balanceStatusElement.className = 'text-xs mt-1 text-green-600 dark:text-green-400';
    } else {
        balanceElement.className = 'text-2xl font-bold mt-2 text-red-600 dark:text-red-400';
        balanceStatusElement.textContent = '⚠️ Déficit';
        balanceStatusElement.className = 'text-xs mt-1 text-red-600 dark:text-red-400';
    }
}

// Atualizar tendências
function updateTrends(trends) {
    const incomeTrendElement = document.getElementById('incomeTrend');
    const expenseTrendElement = document.getElementById('expenseTrend');
    const balanceTrendElement = document.getElementById('balanceTrend');
    
    // Tendência de receitas
    const incomeTrendText = getTrendText(trends.income_trend, trends.income_change);
    incomeTrendElement.innerHTML = incomeTrendText.html;
    
    // Tendência de despesas
    const expenseTrendText = getTrendText(trends.expense_trend, trends.expense_change);
    expenseTrendElement.innerHTML = expenseTrendText.html;
    
    // Tendência de saldo
    const balanceTrendText = getBalanceTrendText(trends.balance_trend, trends.balance_change);
    balanceTrendElement.innerHTML = balanceTrendText.html;
}

// Obter texto de tendência
function getTrendText(trend, change) {
    const changeText = Math.abs(change).toFixed(1) + '%';
    
    if (trend === 'growing') {
        return {
            html: `<span class="text-green-600 dark:text-green-400">↗️ Crescimento de ${changeText}</span>`
        };
    } else if (trend === 'falling') {
        return {
            html: `<span class="text-red-600 dark:text-red-400">↘️ Queda de ${changeText}</span>`
        };
    } else {
        return {
            html: `<span class="text-gray-600 dark:text-gray-400">➡️ Estável (${changeText})</span>`
        };
    }
}

// Obter texto de tendência de saldo
function getBalanceTrendText(trend, change) {
    const changeText = Math.abs(change).toFixed(1) + '%';
    
    if (trend === 'improving') {
        return {
            html: `<span class="text-green-600 dark:text-green-400">✅ Melhorando (${changeText})</span>`
        };
    } else if (trend === 'declining') {
        return {
            html: `<span class="text-red-600 dark:text-red-400">⚠️ Piorando (${changeText})</span>`
        };
    } else {
        return {
            html: `<span class="text-gray-600 dark:text-gray-400">➡️ Estável (${changeText})</span>`
        };
    }
}

// Criar gráfico histórico
function createHistoricalChart(monthlyData) {
    const ctx = document.getElementById('historicalChart');
    const colors = getChartColors();
    
    // Destruir gráfico existente
    if (historicalChart) {
        historicalChart.destroy();
    }
    
    const labels = monthlyData.map(d => d.month);
    const incomes = monthlyData.map(d => d.incomes);
    const expenses = monthlyData.map(d => d.expenses);
    const balances = monthlyData.map(d => d.balance);
    
    historicalChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Receitas',
                    data: incomes,
                    borderColor: colors.income,
                    backgroundColor: colors.income + '20',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                },
                {
                    label: 'Despesas',
                    data: expenses,
                    borderColor: colors.expense,
                    backgroundColor: colors.expense + '20',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                },
                {
                    label: 'Saldo',
                    data: balances,
                    borderColor: colors.balance,
                    backgroundColor: colors.balance + '20',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: colors.text,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    },
                    grid: {
                        color: colors.grid
                    }
                },
                x: {
                    ticks: {
                        color: colors.text
                    },
                    grid: {
                        color: colors.grid
                    }
                }
            }
        }
    });
}

// Criar gráfico de projeções
function createProjectionsChart(projectionsData) {
    const ctx = document.getElementById('projectionsChart');
    const colors = getChartColors();
    
    // Destruir gráfico existente
    if (projectionsChart) {
        projectionsChart.destroy();
    }
    
    const labels = projectionsData.map(d => d.month);
    const projectedIncomes = projectionsData.map(d => d.projected_income);
    const projectedExpenses = projectionsData.map(d => d.projected_expense);
    const projectedBalances = projectionsData.map(d => d.projected_balance);
    
    projectionsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Receitas Projetadas',
                    data: projectedIncomes,
                    backgroundColor: colors.income + '80',
                    borderColor: colors.income,
                    borderWidth: 1,
                },
                {
                    label: 'Despesas Projetadas',
                    data: projectedExpenses,
                    backgroundColor: colors.expense + '80',
                    borderColor: colors.expense,
                    borderWidth: 1,
                },
                {
                    label: 'Saldo Projetado',
                    data: projectedBalances,
                    backgroundColor: colors.balance + '80',
                    borderColor: colors.balance,
                    borderWidth: 1,
                    type: 'line',
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: colors.text,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: colors.text,
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    },
                    grid: {
                        color: colors.grid
                    }
                },
                x: {
                    ticks: {
                        color: colors.text
                    },
                    grid: {
                        color: colors.grid
                    }
                }
            }
        }
    });
}

// Atualizar tabela de dados
function updateDataTable(monthlyData) {
    const tbody = document.getElementById('dataTable');
    tbody.innerHTML = '';
    
    monthlyData.forEach(data => {
        const row = document.createElement('tr');
        row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';
        
        const balanceClass = data.balance >= 0 
            ? 'text-green-600 dark:text-green-400 font-semibold' 
            : 'text-red-600 dark:text-red-400 font-semibold';
        
        const statusBadge = data.balance >= 0
            ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Positivo</span>'
            : '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Negativo</span>';
        
        row.innerHTML = `
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${data.month_full}</td>
            <td class="px-6 py-4 text-right text-green-600 dark:text-green-400">${formatCurrency(data.incomes)}</td>
            <td class="px-6 py-4 text-right text-red-600 dark:text-red-400">${formatCurrency(data.expenses)}</td>
            <td class="px-6 py-4 text-right ${balanceClass}">${formatCurrency(data.balance)}</td>
            <td class="px-6 py-4 text-center">${statusBadge}</td>
        `;
        
        tbody.appendChild(row);
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Carregar dados iniciais
    loadCashFlowData();
    
    // Atualizar ao mudar período
    document.getElementById('periodFilter').addEventListener('change', function() {
        loadCashFlowData();
    });
    
    // Atualizar gráficos ao mudar tema
    const themeToggle = document.querySelector('[data-theme-toggle]');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            setTimeout(() => {
                loadCashFlowData();
            }, 100);
        });
    }
});
