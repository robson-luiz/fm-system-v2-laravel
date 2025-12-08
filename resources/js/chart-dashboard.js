/**
 * Gráficos do Dashboard
 * Chart.js para visualização de receitas vs despesas e uso de cartões
 */

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página do dashboard
    const incomeCtx = document.getElementById('incomeVsExpenseChart');
    const cardCtx = document.getElementById('creditCardUsageChart');
    
    if (!incomeCtx && !cardCtx) {
        // Não estamos no dashboard, não precisa executar
        return;
    }

    // Configuração baseada no tema
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Cores para o tema
    const colors = {
        primary: isDarkMode ? '#60A5FA' : '#3B82F6',
        success: isDarkMode ? '#34D399' : '#10B981',
        danger: isDarkMode ? '#F87171' : '#EF4444',
        warning: isDarkMode ? '#FBBF24' : '#F59E0B',
        text: isDarkMode ? '#F3F4F6' : '#374151',
        grid: isDarkMode ? '#374151' : '#E5E7EB'
    };

    // Obter dados do elemento script que será injetado pelo PHP
    const chartDataElement = document.getElementById('chartData');
    if (!chartDataElement) {
        console.warn('Dados dos gráficos não encontrados');
        return;
    }
    
    const chartData = JSON.parse(chartDataElement.textContent);

    // Gráfico Receitas vs Despesas
    if (incomeCtx && chartData.monthly_comparison) {
        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels: chartData.monthly_comparison.labels,
                datasets: [{
                    label: 'Receitas',
                    data: chartData.monthly_comparison.incomes,
                    borderColor: colors.success,
                    backgroundColor: colors.success + '20',
                    tension: 0.4,
                    fill: false
                }, {
                    label: 'Despesas',
                    data: chartData.monthly_comparison.expenses,
                    borderColor: colors.danger,
                    backgroundColor: colors.danger + '20',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: colors.text
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: colors.text },
                        grid: { color: colors.grid }
                    },
                    y: {
                        ticks: { 
                            color: colors.text,
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        },
                        grid: { color: colors.grid }
                    }
                }
            }
        });
    }

    // Gráfico Uso dos Cartões
    if (cardCtx && chartData.credit_card_usage && chartData.credit_card_usage.length > 0) {
        const cardLabels = chartData.credit_card_usage.map(card => card.name);
        const cardUsage = chartData.credit_card_usage.map(card => card.usage_percentage);
        
        new Chart(cardCtx, {
            type: 'doughnut',
            data: {
                labels: cardLabels,
                datasets: [{
                    data: cardUsage,
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        '#8B5CF6',
                        '#06B6D4'
                    ],
                    borderWidth: 2,
                    borderColor: isDarkMode ? '#1F2937' : '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.text,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
