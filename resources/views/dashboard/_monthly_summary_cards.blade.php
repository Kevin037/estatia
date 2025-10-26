<!-- Monthly Summary Cards: Current Month vs Previous Month -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
    <!-- Revenue Card -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div id="revenueCardLoading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
        </div>
        <div id="revenueCard" class="hidden">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-100">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Revenue (This Month)</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" id="revenueValue">Rp 0</div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold" id="revenueChange">
                                <span>—</span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Card -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div id="poCardLoading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div id="poCard" class="hidden">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Purchase Orders (This Month)</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" id="poValue">0</div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold" id="poChange">
                                <span>—</span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit Card -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div id="profitCardLoading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        </div>
        <div id="profitCard" class="hidden">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Net Profit (This Month)</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" id="profitValue">Rp 0</div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold" id="profitChange">
                                <span>—</span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Currency formatter for IDR
    const currencyFormatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    // Integer formatter
    const integerFormatter = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    // Helper function to create change element with arrow and color
    function createChangeElement(percentChange, isCount = false) {
        if (percentChange === null || percentChange === undefined) {
            return '<span class="text-gray-500">—</span>';
        }

        const isPositive = percentChange >= 0;
        const colorClass = isPositive ? 'text-green-600' : 'text-red-600';
        const arrowIcon = isPositive 
            ? '<svg class="self-center flex-shrink-0 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>'
            : '<svg class="self-center flex-shrink-0 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';

        const sign = isPositive ? '+' : '';
        return `<span class="${colorClass}">${arrowIcon}<span>${sign}${percentChange.toFixed(2)}%</span></span>`;
    }

    // Fetch and render monthly summary
    async function loadMonthlySummary() {
        const revenueLoading = document.getElementById('revenueCardLoading');
        const revenueCard = document.getElementById('revenueCard');
        const poLoading = document.getElementById('poCardLoading');
        const poCard = document.getElementById('poCard');
        const profitLoading = document.getElementById('profitCardLoading');
        const profitCard = document.getElementById('profitCard');

        try {
            const response = await fetch('{{ route('reports.monthly_summary') }}');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const metrics = data.metrics;

            // Update Revenue Card
            document.getElementById('revenueValue').textContent = 
                currencyFormatter.format(metrics.total_revenue.current);
            document.getElementById('revenueChange').innerHTML = 
                createChangeElement(metrics.total_revenue.percent_change);

            // Update Purchase Orders Card
            document.getElementById('poValue').textContent = 
                integerFormatter.format(metrics.total_purchase_orders_count.current);
            document.getElementById('poChange').innerHTML = 
                createChangeElement(metrics.total_purchase_orders_count.percent_change, true);

            // Update Net Profit Card
            document.getElementById('profitValue').textContent = 
                currencyFormatter.format(metrics.total_net_profit.current);
            document.getElementById('profitChange').innerHTML = 
                createChangeElement(metrics.total_net_profit.percent_change);

            // Hide loading, show cards
            revenueLoading.classList.add('hidden');
            revenueCard.classList.remove('hidden');
            poLoading.classList.add('hidden');
            poCard.classList.remove('hidden');
            profitLoading.classList.add('hidden');
            profitCard.classList.remove('hidden');

        } catch (error) {
            console.error('Error loading monthly summary:', error);
            
            // Show fallback values on error
            document.getElementById('revenueValue').textContent = 'Rp 0';
            document.getElementById('revenueChange').innerHTML = '<span class="text-gray-500">—</span>';
            document.getElementById('poValue').textContent = '0';
            document.getElementById('poChange').innerHTML = '<span class="text-gray-500">—</span>';
            document.getElementById('profitValue').textContent = 'Rp 0';
            document.getElementById('profitChange').innerHTML = '<span class="text-gray-500">—</span>';

            // Hide loading, show cards with fallback
            revenueLoading.classList.add('hidden');
            revenueCard.classList.remove('hidden');
            poLoading.classList.add('hidden');
            poCard.classList.remove('hidden');
            profitLoading.classList.add('hidden');
            profitCard.classList.remove('hidden');
        }
    }

    // Load summary on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadMonthlySummary();
    });
</script>
