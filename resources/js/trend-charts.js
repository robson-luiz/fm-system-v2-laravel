// Variáveis globais para os gráficos
let trendChart = null;
let projectionChart = null;

/**
 * Inicializa os gráficos de tendências
 */
function initializeTrendCharts() {
    // Criar gráfico de tendências (vazio inicialmente)
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            color: isDarkMode() ? '#D1D5DB' : '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode() ? '#1F2937' : '#FFFFFF',
                        titleColor: isDarkMode() ? '#F9FAFB' : '#111827',
                        bodyColor: isDarkMode() ? '#D1D5DB' : '#374151',
                        borderColor: isDarkMode() ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: isDarkMode() ? '#374151' : '#E5E7EB'
                        },
                        ticks: {
                            color: isDarkMode() ? '#9CA3AF' : '#6B7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: isDarkMode() ? '#374151' : '#E5E7EB'
                        },
                        ticks: {
                            color: isDarkMode() ? '#9CA3AF' : '#6B7280',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                }
            }
        });
    }

    // Criar gráfico de projeções (vazio inicialmente)
    const projectionCtx = document.getElementById('projectionChart');
    if (projectionCtx) {
        projectionChart = new Chart(projectionCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            color: isDarkMode() ? '#D1D5DB' : '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode() ? '#1F2937' : '#FFFFFF',
                        titleColor: isDarkMode() ? '#F9FAFB' : '#111827',
                        bodyColor: isDarkMode() ? '#D1D5DB' : '#374151',
                        borderColor: isDarkMode() ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            color: isDarkMode() ? '#374151' : '#E5E7EB'
                        },
                        ticks: {
                            color: isDarkMode() ? '#9CA3AF' : '#6B7280'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: {
                            color: isDarkMode() ? '#374151' : '#E5E7EB'
                        },
                        ticks: {
                            color: isDarkMode() ? '#9CA3AF' : '#6B7280',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                }
            }
        });
    }

    // Carregar dados iniciais
    loadInitialData();
}

/**
 * Carrega dados iniciais
 */
async function loadInitialData() {
    try {
        // Carregar dados históricos
        const historicalResponse = await fetch('/trends/historical?months=12');
        const historicalData = await historicalResponse.json();
        
        if (historicalData.success) {
            updateTrendChart(historicalData.data);
        }
        
        // Carregar projeções
        const projectionsResponse = await fetch('/trends/projections?historical_months=6&future_months=6');
        const projectionsData = await projectionsResponse.json();
        
        if (projectionsData.success) {
            updateProjectionChart(projectionsData.projections);
        }
    } catch (error) {
        console.error('Erro ao carregar dados iniciais:', error);
    }
}

/**
 * Atualiza o gráfico de tendências
 */
function updateTrendChart(data) {
    if (!trendChart || !data || data.length === 0) return;
    
    // Extrair labels (meses) do primeiro dataset
    const labels = data[0]?.data?.map(item => item.month) || [];
    
    // Criar datasets por categoria
    const datasets = data
        .filter(category => {
            // Incluir apenas categorias com dados
            const hasData = category.data.some(item => item.amount > 0);
            return hasData;
        })
        .map(category => ({
            label: `${category.icon} ${category.category}`,
            data: category.data.map(item => item.amount),
            borderColor: category.color,
            backgroundColor: category.color + '20',
            borderWidth: 2,
            tension: 0.4,
            fill: false,
            pointRadius: 4,
            pointHoverRadius: 6
        }));
    
    // Atualizar gráfico
    trendChart.data.labels = labels;
    trendChart.data.datasets = datasets;
    trendChart.update();
}

/**
 * Atualiza o gráfico de projeções
 */
function updateProjectionChart(data) {
    if (!projectionChart || !data || data.length === 0) return;
    
    // Extrair labels (meses) do primeiro dataset
    const labels = data[0]?.projections?.map(item => item.month) || [];
    
    // Criar datasets por categoria
    const datasets = data
        .filter(category => category.average > 0) // Apenas categorias com histórico
        .map(category => ({
            label: `${category.icon} ${category.category}`,
            data: category.projections.map(item => item.amount),
            backgroundColor: category.color,
            borderColor: category.color,
            borderWidth: 1
        }));
    
    // Atualizar gráfico
    projectionChart.data.labels = labels;
    projectionChart.data.datasets = datasets;
    projectionChart.update();
}

/**
 * Detecta se está em modo escuro
 */
function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

/**
 * Atualiza cores do gráfico quando o tema muda
 */
function updateChartColors() {
    if (trendChart) {
        trendChart.options.plugins.legend.labels.color = isDarkMode() ? '#D1D5DB' : '#374151';
        trendChart.options.scales.x.grid.color = isDarkMode() ? '#374151' : '#E5E7EB';
        trendChart.options.scales.x.ticks.color = isDarkMode() ? '#9CA3AF' : '#6B7280';
        trendChart.options.scales.y.grid.color = isDarkMode() ? '#374151' : '#E5E7EB';
        trendChart.options.scales.y.ticks.color = isDarkMode() ? '#9CA3AF' : '#6B7280';
        trendChart.update();
    }
    
    if (projectionChart) {
        projectionChart.options.plugins.legend.labels.color = isDarkMode() ? '#D1D5DB' : '#374151';
        projectionChart.options.scales.x.grid.color = isDarkMode() ? '#374151' : '#E5E7EB';
        projectionChart.options.scales.x.ticks.color = isDarkMode() ? '#9CA3AF' : '#6B7280';
        projectionChart.options.scales.y.grid.color = isDarkMode() ? '#374151' : '#E5E7EB';
        projectionChart.options.scales.y.ticks.color = isDarkMode() ? '#9CA3AF' : '#6B7280';
        projectionChart.update();
    }
}

// Observar mudanças no tema
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                updateChartColors();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
}

// Variável global para controlar o período atual
let currentPeriod = 12;

/**
 * Muda o período de análise (6, 12 ou 24 meses)
 */
function changePeriod(months) {
    currentPeriod = months;
    
    // Atualizar botões
    ['6', '12', '24'].forEach(m => {
        const btn = document.getElementById(`btn-${m}`);
        if (m == months) {
            btn.className = 'px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white';
        } else {
            btn.className = 'px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600';
        }
    });
    
    // Recarregar dados
    loadTrendData(months);
}

/**
 * Carrega todos os dados de tendências via AJAX
 */
async function loadTrendData(months) {
    try {
        // Carregar tendências e resumo
        const trendsResponse = await fetch(`/trends/data?months=${months}`);
        const trendsData = await trendsResponse.json();
        
        if (trendsData.success) {
            updateSummaryCards(trendsData.summary, months);
            updateTrendsTable(trendsData.trends);
        }
        
        // Carregar dados históricos para gráfico
        const historicalResponse = await fetch(`/trends/historical?months=${months}`);
        const historicalData = await historicalResponse.json();
        
        if (historicalData.success) {
            updateTrendChart(historicalData.data);
        }
        
        // Carregar projeções
        const projectionsResponse = await fetch('/trends/projections?historical_months=6&future_months=6');
        const projectionsData = await projectionsResponse.json();
        
        if (projectionsData.success) {
            updateProjectionChart(projectionsData.projections);
        }
        
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
    }
}

/**
 * Atualiza os cards de resumo no topo da página
 */
function updateSummaryCards(summary, months) {
    document.getElementById('stat-total').textContent = 
        `R$ ${summary.total_spent.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    
    document.getElementById('stat-average').textContent = 
        `R$ ${summary.monthly_average.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    
    const trendElement = document.getElementById('stat-trend');
    const changeValue = summary.overall_change;
    const color = changeValue > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
    trendElement.innerHTML = `<span class="${color}">${changeValue > 0 ? '+' : ''}${changeValue.toFixed(2)}%</span>`;
    
    document.getElementById('period-label').textContent = months;
}

/**
 * Atualiza a tabela de tendências detalhada
 */
function updateTrendsTable(trends) {
    const tbody = document.getElementById('trends-table-body');
    tbody.innerHTML = '';
    
    trends.forEach(trend => {
        const trendBadge = trend.trend === 'up' ? 
            '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">📈 Crescimento</span>' :
            trend.trend === 'down' ?
            '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">📉 Redução</span>' :
            '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400">➡️ Estável</span>';
        
        const changeColor = trend.percent_change > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
        
        tbody.innerHTML += `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">${trend.icon}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${trend.category}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                    R$ ${trend.total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-400">
                    R$ ${trend.average.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">${trendBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold">
                    <span class="${changeColor}">${trend.percent_change > 0 ? '+' : ''}${trend.percent_change.toFixed(2)}%</span>
                </td>
            </tr>
        `;
    });
}

// Inicializar na carga da página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de tendências
    if (document.getElementById('trendChart')) {
        initializeTrendCharts();
    }
});

// Expor funções globalmente para uso na view Blade
window.initializeTrendCharts = initializeTrendCharts;
window.updateTrendChart = updateTrendChart;
window.updateProjectionChart = updateProjectionChart;
window.changePeriod = changePeriod;
window.loadTrendData = loadTrendData;
window.updateSummaryCards = updateSummaryCards;
window.updateTrendsTable = updateTrendsTable;
