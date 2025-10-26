<!-- Monthly Top Products (Drilldown) Chart -->
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue by Product</h3>
            <p class="text-sm text-gray-500">Click any month to see top products</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Chart Type Toggle -->
            <div class="flex items-center border border-gray-300 rounded-lg p-1">
                <button id="toggleBarChart" class="px-3 py-1 text-sm font-medium rounded bg-emerald-600 text-white transition">
                    Bar Chart
                </button>
                <button id="togglePieChart" class="px-3 py-1 text-sm font-medium rounded text-gray-700 hover:bg-gray-100 transition">
                    Pie View
                </button>
            </div>
            
            <!-- Range Selector -->
            <div class="flex items-center gap-2">
                <label for="monthRangeTopProducts" class="text-sm font-medium text-gray-700">Range:</label>
                <select id="monthRangeTopProducts" class="form-select text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="6">6 Months</option>
                    <option value="12" selected>12 Months</option>
                    <option value="24">24 Months</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Loading State -->
    <div id="topProductsLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
    </div>
    
    <!-- Error State -->
    <div id="topProductsError" class="hidden rounded-md bg-red-50 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-800" id="topProductsErrorMessage">Failed to load chart data</p>
            </div>
        </div>
    </div>
    
    <!-- Chart Canvas -->
    <div id="topProductsChartContainer" class="hidden">
        <canvas id="monthlyTopProductsChart" height="80"></canvas>
    </div>
    
    <!-- Pie Chart Container -->
    <div id="topProductsPieContainer" class="hidden">
        <div class="text-center mb-4">
            <h4 class="text-md font-semibold text-gray-800" id="pieChartTitle">Top 5 Products - Latest Month</h4>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <canvas id="monthlyTopProductsPieChart" height="200"></canvas>
            </div>
            <div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody id="pieChartTable" class="bg-white divide-y divide-gray-200">
                        <!-- Dynamic content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Drilldown Modal (Bootstrap 5 Style with Tailwind) -->
<div id="monthlyTopProductsModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTopProductsModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white" id="modalTitle">Top 5 Products</h3>
                        <p class="text-sm text-emerald-100" id="modalSubtitle">Revenue breakdown</p>
                    </div>
                    <button type="button" onclick="closeTopProductsModal()" class="text-white hover:text-gray-200 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <!-- Top K Selector -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <label for="topKSelector" class="text-sm font-medium text-gray-700">Show Top:</label>
                        <select id="topKSelector" class="form-select text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="3">3 Products</option>
                            <option value="5" selected>5 Products</option>
                            <option value="10">10 Products</option>
                            <option value="15">15 Products</option>
                            <option value="20">20 Products</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Total Month Revenue:</span>
                        <span id="modalTotalRevenue" class="font-bold text-emerald-600">Rp 0</span>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="modalLoading" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600"></div>
                </div>

                <!-- Products Table -->
                <div id="modalTableContainer" class="hidden overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue (Rp)</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% of Month</th>
                            </tr>
                        </thead>
                        <tbody id="modalProductsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2">
                <button type="button" onclick="closeTopProductsModal()" class="btn btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library (if not already loaded) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Global variables
    let topProductsBarChart = null;
    let topProductsPieChart = null;
    let currentChartData = null;
    let currentMonthsData = [];
    let selectedMonth = null;
    
    // Currency formatter
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    };
    
    // Fetch and render bar chart data
    async function loadTopProductsChart(months = 12) {
        const loadingEl = document.getElementById('topProductsLoading');
        const errorEl = document.getElementById('topProductsError');
        const containerEl = document.getElementById('topProductsChartContainer');
        
        loadingEl.classList.remove('hidden');
        errorEl.classList.add('hidden');
        containerEl.classList.add('hidden');
        
        try {
            const response = await fetch(`{{ route('reports.monthly_top_products') }}?months=${months}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            currentChartData = data;
            currentMonthsData = data.months_data || [];
            
            loadingEl.classList.add('hidden');
            containerEl.classList.remove('hidden');
            
            renderBarChart(data);
            
        } catch (error) {
            console.error('Error loading chart data:', error);
            loadingEl.classList.add('hidden');
            errorEl.classList.remove('hidden');
            document.getElementById('topProductsErrorMessage').textContent = 
                'Failed to load chart data. Please try again.';
        }
    }
    
    // Render Bar Chart
    function renderBarChart(data) {
        const ctx = document.getElementById('monthlyTopProductsChart');
        
        if (topProductsBarChart) {
            topProductsBarChart.destroy();
        }
        
        topProductsBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Total Revenue',
                    data: data.revenues,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 4,
                    hoverBackgroundColor: 'rgba(16, 185, 129, 0.9)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const monthData = currentMonthsData[index];
                        openDrilldownModal(monthData.year, monthData.month, monthData.label);
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280',
                            callback: function(value) {
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
    
    // Load and render pie chart for latest month
    async function loadPieChart() {
        const loadingEl = document.getElementById('topProductsLoading');
        const pieContainer = document.getElementById('topProductsPieContainer');
        
        loadingEl.classList.remove('hidden');
        pieContainer.classList.add('hidden');
        
        try {
            // Get latest month data
            const latestMonth = currentMonthsData[currentMonthsData.length - 1];
            const response = await fetch(
                `{{ url('/reports/monthly-top-products') }}/${latestMonth.year}/${latestMonth.month}?top=5`
            );
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            loadingEl.classList.add('hidden');
            pieContainer.classList.remove('hidden');
            
            document.getElementById('pieChartTitle').textContent = 
                `Top 5 Products - ${data.label} (${formatCurrency(data.total_month_revenue)})`;
            
            renderPieChart(data);
            
        } catch (error) {
            console.error('Error loading pie chart:', error);
            loadingEl.classList.add('hidden');
            alert('Failed to load pie chart data');
        }
    }
    
    // Render Pie Chart
    function renderPieChart(data) {
        const ctx = document.getElementById('monthlyTopProductsPieChart');
        
        if (topProductsPieChart) {
            topProductsPieChart.destroy();
        }
        
        const labels = data.top_products.map(p => p.product_name);
        const revenues = data.top_products.map(p => p.revenue);
        const percentages = data.top_products.map(p => p.percentage);
        
        const colors = [
            'rgba(16, 185, 129, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
        ];
        
        topProductsPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: revenues,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = percentages[context.dataIndex];
                                return `${label}: ${formatCurrency(value)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Populate table
        const tableBody = document.getElementById('pieChartTable');
        tableBody.innerHTML = data.top_products.map((product, index) => `
            <tr>
                <td class="px-4 py-2 text-sm text-gray-900">${product.product_name}</td>
                <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">${formatCurrency(product.revenue)}</td>
                <td class="px-4 py-2 text-sm text-right font-medium text-emerald-600">${product.percentage}%</td>
            </tr>
        `).join('');
    }
    
    // Open drilldown modal
    async function openDrilldownModal(year, month, label) {
        selectedMonth = { year, month, label };
        const modal = document.getElementById('monthlyTopProductsModal');
        const topK = document.getElementById('topKSelector').value;
        
        modal.classList.remove('hidden');
        document.getElementById('modalTitle').textContent = `Top ${topK} Products`;
        document.getElementById('modalSubtitle').textContent = label;
        
        await loadDrilldownData(year, month, topK);
    }
    
    // Load drilldown data
    async function loadDrilldownData(year, month, top = 5) {
        const loadingEl = document.getElementById('modalLoading');
        const tableContainer = document.getElementById('modalTableContainer');
        
        loadingEl.classList.remove('hidden');
        tableContainer.classList.add('hidden');
        
        try {
            const response = await fetch(
                `{{ url('/reports/monthly-top-products') }}/${year}/${month}?top=${top}`
            );
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            loadingEl.classList.add('hidden');
            tableContainer.classList.remove('hidden');
            
            document.getElementById('modalTotalRevenue').textContent = 
                formatCurrency(data.total_month_revenue);
            
            const tableBody = document.getElementById('modalProductsTable');
            tableBody.innerHTML = data.top_products.map(product => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full ${
                            product.rank === 1 ? 'bg-yellow-100 text-yellow-800' :
                            product.rank === 2 ? 'bg-gray-100 text-gray-800' :
                            product.rank === 3 ? 'bg-orange-100 text-orange-800' :
                            'bg-blue-100 text-blue-800'
                        }">
                            ${product.rank}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${product.product_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                        ${formatCurrency(product.revenue)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            ${product.percentage}%
                        </span>
                    </td>
                </tr>
            `).join('');
            
        } catch (error) {
            console.error('Error loading drilldown data:', error);
            loadingEl.classList.add('hidden');
            alert('Failed to load product data');
        }
    }
    
    // Close drilldown modal
    function closeTopProductsModal() {
        document.getElementById('monthlyTopProductsModal').classList.add('hidden');
    }
    
    // Toggle between bar and pie charts
    function toggleChartView(view) {
        const barBtn = document.getElementById('toggleBarChart');
        const pieBtn = document.getElementById('togglePieChart');
        const barContainer = document.getElementById('topProductsChartContainer');
        const pieContainer = document.getElementById('topProductsPieContainer');
        
        if (view === 'bar') {
            barBtn.classList.add('bg-emerald-600', 'text-white');
            barBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
            pieBtn.classList.remove('bg-emerald-600', 'text-white');
            pieBtn.classList.add('text-gray-700', 'hover:bg-gray-100');
            
            barContainer.classList.remove('hidden');
            pieContainer.classList.add('hidden');
        } else {
            pieBtn.classList.add('bg-emerald-600', 'text-white');
            pieBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
            barBtn.classList.remove('bg-emerald-600', 'text-white');
            barBtn.classList.add('text-gray-700', 'hover:bg-gray-100');
            
            barContainer.classList.add('hidden');
            pieContainer.classList.remove('hidden');
            
            if (!topProductsPieChart) {
                loadPieChart();
            }
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial chart (12 months)
        loadTopProductsChart(12);
        
        // Range selector change handler
        document.getElementById('monthRangeTopProducts').addEventListener('change', function(e) {
            const months = parseInt(e.target.value);
            loadTopProductsChart(months);
        });
        
        // Chart toggle handlers
        document.getElementById('toggleBarChart').addEventListener('click', () => toggleChartView('bar'));
        document.getElementById('togglePieChart').addEventListener('click', () => toggleChartView('pie'));
        
        // Top K selector change handler
        document.getElementById('topKSelector').addEventListener('change', function(e) {
            if (selectedMonth) {
                const top = parseInt(e.target.value);
                document.getElementById('modalTitle').textContent = `Top ${top} Products`;
                loadDrilldownData(selectedMonth.year, selectedMonth.month, top);
            }
        });
    });
</script>
