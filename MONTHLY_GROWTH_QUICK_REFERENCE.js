// ============================================================================
// QUICK REFERENCE: 12-Month Sales & Profit Growth Feature
// ============================================================================

// ----------------------------------------------------------------------------
// BACKEND CODE (Copy-Paste Ready)
// ----------------------------------------------------------------------------

// 1. SERVICE METHOD (app/Services/ReportService.php)
// Add this method to your ReportService class:

public function getMonthlySalesAndProfit(int $months = 12): array
{
    // Ensure months is between 1 and 36
    $months = max(1, min(36, $months));

    // Get current date
    $endDate = Carbon::now();
    
    // Calculate start date (N months ago)
    $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

    $labels = [];
    $salesData = [];
    $profitData = [];

    // Loop through each month from start to end
    $currentMonth = $startDate->copy();
    
    while ($currentMonth <= $endDate) {
        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd = $currentMonth->copy()->endOfMonth();

        // Label format: "Nov 2024"
        $labels[] = $monthStart->format('M Y');

        // Calculate total sales for the month (completed orders)
        $totalSales = Order::whereBetween('dt', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('total');

        // Calculate total HPP/COGS for the month (completed purchase orders)
        $totalHpp = PurchaseOrder::whereBetween('dt', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('total');

        // Calculate total expenses for the month (expense account journal entries)
        $totalExpenses = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.dt', [$monthStart, $monthEnd])
            ->whereNotNull('journal_entries.credit')
            ->where('accounts.id', 'like', '5%')
            ->sum('journal_entries.credit');

        // Calculate net profit: Sales - HPP - Expenses
        $netProfit = $totalSales - $totalHpp - $totalExpenses;

        // Round to 2 decimals and cast to float
        $salesData[] = round((float) $totalSales, 2);
        $profitData[] = round((float) $netProfit, 2);

        // Move to next month
        $currentMonth->addMonth();
    }

    return [
        'labels' => $labels,
        'sales' => $salesData,
        'profit' => $profitData,
        'meta' => [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'months' => $months,
        ]
    ];
}

// ----------------------------------------------------------------------------

// 2. CONTROLLER METHOD (app/Http/Controllers/ReportController.php)
// Add this method to your ReportController class:

public function monthlyGrowth(Request $request)
{
    // Validate months parameter
    $request->validate([
        'months' => 'nullable|integer|min:1|max:36',
    ]);

    // Get months parameter (default: 12)
    $months = $request->input('months', 12);

    // Get data from service
    $data = $this->reportService->getMonthlySalesAndProfit($months);

    // Return JSON response
    return response()->json($data);
}

// ----------------------------------------------------------------------------

// 3. ROUTE (routes/web.php)
// Add this route inside your auth middleware group:

Route::get('/reports/monthly-growth', [\App\Http\Controllers\ReportController::class, 'monthlyGrowth'])
    ->name('reports.monthly_growth');

// ----------------------------------------------------------------------------

// 4. DASHBOARD INTEGRATION (resources/views/dashboard.blade.php)
// Add this line where you want the chart to appear:

@include('dashboard._monthly_growth')

// ----------------------------------------------------------------------------
// USAGE EXAMPLES
// ----------------------------------------------------------------------------

// Web Browser:
// http://your-domain.com/dashboard (view chart)

// API Calls:
// GET /reports/monthly-growth          -> Last 12 months (default)
// GET /reports/monthly-growth?months=6  -> Last 6 months
// GET /reports/monthly-growth?months=24 -> Last 24 months

// JavaScript Fetch:
fetch('/reports/monthly-growth?months=12')
    .then(response => response.json())
    .then(data => {
        console.log(data.labels);  // ['Nov 2024', 'Dec 2024', ...]
        console.log(data.sales);   // [123456.78, 234567.89, ...]
        console.log(data.profit);  // [23456.78, 34567.89, ...]
    });

// PHP Usage:
$reportService = app(ReportService::class);
$data = $reportService->getMonthlySalesAndProfit(12);

// ----------------------------------------------------------------------------
// VERIFICATION COMMANDS
// ----------------------------------------------------------------------------

// Check route registration:
php artisan route:list --name=reports.monthly_growth

// Check for errors:
php artisan about

// Test API endpoint:
curl http://localhost/reports/monthly-growth?months=12

// ----------------------------------------------------------------------------
// CUSTOMIZATION TIPS
// ----------------------------------------------------------------------------

// Change default range (edit _monthly_growth.blade.php, line ~267):
loadChartData(12); // Change to 6, 24, etc.

// Change chart colors (edit _monthly_growth.blade.php):
// Sales: line ~143, change borderColor: '#10b981'
// Profit: line ~161, change borderColor: '#3b82f6'

// Add new range option (edit _monthly_growth.blade.php, line ~20):
<option value="36">36 Months</option>

// Change currency format (edit _monthly_growth.blade.php, line ~48):
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD', // Change from IDR to USD
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// ----------------------------------------------------------------------------
// FILE STRUCTURE
// ----------------------------------------------------------------------------
/*
app/
├── Services/
│   └── ReportService.php                      [MODIFIED - added method]
└── Http/
    └── Controllers/
        └── ReportController.php                [MODIFIED - added method]

routes/
└── web.php                                     [MODIFIED - added route]

resources/
└── views/
    └── dashboard/
        ├── _monthly_growth.blade.php          [NEW - chart component]
        └── dashboard.blade.php                 [MODIFIED - include chart]

Documentation:
└── MONTHLY_GROWTH_IMPLEMENTATION.md            [NEW - full documentation]
*/

// ============================================================================
// STATUS: ✅ COMPLETE & PRODUCTION READY
// ============================================================================
