<!-- 12-Month Sales & Profit Growth Chart -->
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Sales & Profit Growth</h3>
            <p class="text-sm text-gray-500">Last 12 months performance</p>
        </div>
        <div class="flex items-center gap-2">
            <label for="monthRange" class="text-sm font-medium text-gray-700">Range:</label>
            <select id="monthRange" class="form-select text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                <option value="6">6 Months</option>
                <option value="12" selected>12 Months</option>
                <option value="24">24 Months</option>
            </select>
        </div>
    </div>
    
    <!-- Loading State -->
    <div id="chartLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
    </div>
    
    <!-- Error State -->
    <div id="chartError" class="hidden rounded-md bg-red-50 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-800" id="chartErrorMessage">Failed to load chart data</p>
            </div>
        </div>
    </div>
    
    <!-- Chart Canvas -->
    <div id="chartContainer" class="hidden">
        <canvas id="monthlyGrowthChart" height="80"></canvas>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Chart instance
    let monthlyGrowthChart = null;
    
    // Currency formatter
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    };
    
    // Number formatter with thousand separators
    const formatNumber = (value) => {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    };
    
    // Fetch and render chart data
    async function loadChartData(months = 12) {
        const loadingEl = document.getElementById('chartLoading');
        const errorEl = document.getElementById('chartError');
        const containerEl = document.getElementById('chartContainer');
        
        // Show loading
        loadingEl.classList.remove('hidden');
        errorEl.classList.add('hidden');
        containerEl.classList.add('hidden');
        
        try {
            const response = await fetch(`{{ route('reports.monthly_growth') }}?months=${months}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Hide loading, show chart
            loadingEl.classList.add('hidden');
            containerEl.classList.remove('hidden');
            
            // Render chart
            renderChart(data);
            
        } catch (error) {
            console.error('Error loading chart data:', error);
            loadingEl.classList.add('hidden');
            errorEl.classList.remove('hidden');
            document.getElementById('chartErrorMessage').textContent = 
                'Failed to load chart data. Please try again.';
        }
    }
    
    // Render Chart.js chart
    function renderChart(data) {
        const ctx = document.getElementById('monthlyGrowthChart');
        
        // Destroy existing chart if it exists
        if (monthlyGrowthChart) {
            monthlyGrowthChart.destroy();
        }
        
        // Determine point colors for profit (red for negative, green for positive)
        const profitPointColors = data.profit.map(value => 
            value < 0 ? '#ef4444' : '#10b981'
        );
        
        // Create gradient for sales
        const salesGradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        salesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        salesGradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');
        
        // Create new chart
        monthlyGrowthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Sales',
                        data: data.sales,
                        borderColor: '#10b981',
                        backgroundColor: salesGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#fff',
                    },
                    {
                        label: 'Net Profit',
                        data: data.profit,
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: profitPointColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: profitPointColors,
                        pointHoverBorderColor: '#fff',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += formatCurrency(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280',
                            callback: function(value) {
                                // Format Y-axis labels as abbreviated currency
                                if (value >= 1000000000) {
                                    return 'Rp ' + (value / 1000000000).toFixed(1) + 'B';
                                } else if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(1) + 'K';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Load initial chart data (12 months)
    document.addEventListener('DOMContentLoaded', function() {
        loadChartData(12);
        
        // Handle range selector change
        document.getElementById('monthRange').addEventListener('change', function(e) {
            const months = parseInt(e.target.value);
            loadChartData(months);
        });
    });
</script>
