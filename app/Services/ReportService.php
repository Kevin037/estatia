<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Calculate Profit & Loss for a given date range.
     * 
     * @param string $startDate Format: yyyy-mm-dd
     * @param string $endDate Format: yyyy-mm-dd
     * @return array Associative array with financial data
     */
    public function calculateProfitLoss($startDate, $endDate)
    {
        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Total Sales: Sum of completed orders within date range
        $totalSales = Order::whereBetween('dt', [$start, $end])
            ->where('status', 'completed')
            ->sum('total');

        // Total HPP (Cost of Goods Sold): Sum of completed purchase orders within date range
        $totalHpp = PurchaseOrder::whereBetween('dt', [$start, $end])
            ->where('status', 'completed')
            ->sum('total');

        // Total Expenses: Calculate from journal entries for expense accounts
        // Expense accounts typically have IDs starting with 5 or specific parent_id
        // Using credit entries as expenses (standard accounting practice)
        $totalExpenses = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.dt', [$start, $end])
            ->whereNotNull('journal_entries.credit')
            ->where(function($query) {
                // Filter for expense accounts (ID starts with 5)
                $query->where('accounts.id', 'like', '5%')
                    // Or has parent that is an expense account
                    ->orWhereIn('accounts.parent_id', function($subquery) {
                        $subquery->select('id')
                            ->from('accounts')
                            ->where('id', 'like', '5%');
                    });
            })
            ->sum('journal_entries.credit');

        // Gross Profit: Total Sales - Total HPP
        $grossProfit = $totalSales - $totalHpp;

        // Net Profit: Gross Profit - Total Expenses
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'total_sales' => $totalSales,
            'total_hpp' => $totalHpp,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Get revenue trend data for charts
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getRevenueTrend($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return Order::whereBetween('dt', [$start, $end])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(dt) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top expenses by category
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopExpenses($startDate, $endDate, $limit = 10)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.dt', [$start, $end])
            ->whereNotNull('journal_entries.credit')
            ->where('accounts.id', 'like', '5%')
            ->select(
                'accounts.name as account_name',
                DB::raw('SUM(journal_entries.credit) as total')
            )
            ->groupBy('accounts.id', 'accounts.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate Balance Sheet as of a specific date.
     * 
     * @param string $asOfDate Snapshot date for balance sheet (yyyy-mm-dd)
     * @param string|null $plStartDate Optional P&L start date for net profit calculation
     * @param string|null $plEndDate Optional P&L end date for net profit calculation
     * @return array Associative array with balance sheet data
     */
    public function calculateBalanceSheet(string $asOfDate, ?string $plStartDate = null, ?string $plEndDate = null): array
    {
        // Parse the as-of date
        $asOf = Carbon::parse($asOfDate)->endOfDay();

        // Default P&L period to start of current month -> asOfDate if not provided
        if ($plStartDate === null || $plEndDate === null) {
            $plStartDate = Carbon::parse($asOfDate)->startOfMonth()->format('Y-m-d');
            $plEndDate = $asOfDate;
        }

        // Calculate net profit using existing P&L function
        $profitLoss = $this->calculateProfitLoss($plStartDate, $plEndDate);
        $netProfit = $profitLoss['net_profit'];

        // Calculate Assets (accounts starting with 1)
        $assetsData = $this->getAccountBalances($asOf, '1%', 'debit');
        $assetsTotal = $assetsData['total'];
        $assetsBreakdown = $assetsData['breakdown'];

        // Calculate Liabilities (accounts starting with 2)
        $liabilitiesData = $this->getAccountBalances($asOf, '2%', 'credit');
        $liabilitiesTotal = $liabilitiesData['total'];
        $liabilitiesBreakdown = $liabilitiesData['breakdown'];

        // Calculate Equities (accounts starting with 3)
        $equitiesData = $this->getAccountBalances($asOf, '3%', 'credit');
        $baseEquitiesTotal = $equitiesData['total'];
        $equitiesBreakdown = $equitiesData['breakdown'];

        // Add net profit to equities (retained earnings)
        $equitiesTotal = $baseEquitiesTotal + $netProfit;

        // Add net profit as a line item in equities breakdown if not zero
        if ($netProfit != 0) {
            $equitiesBreakdown[] = [
                'account_name' => 'Net Profit (Current Period)',
                'amount' => $netProfit
            ];
        }

        // Sort equities breakdown by amount descending
        usort($equitiesBreakdown, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        // Check if balance sheet is balanced
        $balanced = $this->isBalanced($assetsTotal, $liabilitiesTotal, $equitiesTotal);

        return [
            'as_of' => $asOfDate,
            'assets_total' => $assetsTotal,
            'liabilities_total' => $liabilitiesTotal,
            'equities_total' => $equitiesTotal,
            'net_profit' => $netProfit,
            'balanced' => $balanced,
            'assets_breakdown' => $assetsBreakdown,
            'liabilities_breakdown' => $liabilitiesBreakdown,
            'equities_breakdown' => $equitiesBreakdown,
            'pl_start_date' => $plStartDate,
            'pl_end_date' => $plEndDate,
        ];
    }

    /**
     * Get account balances for a specific account type as of a date.
     * 
     * @param Carbon $asOfDate
     * @param string $accountPattern Account ID pattern (e.g., '1%' for assets)
     * @param string $balanceType 'debit' or 'credit' - determines how to calculate balance
     * @return array ['total' => float, 'breakdown' => array]
     */
    private function getAccountBalances(Carbon $asOfDate, string $accountPattern, string $balanceType): array
    {
        // Get accounts matching the pattern with parent_id not null (detail accounts)
        $accounts = DB::table('accounts')
            ->where('id', 'like', $accountPattern)
            ->whereNotNull('parent_id')
            ->select('id', 'name')
            ->get();

        $breakdown = [];
        $total = 0;

        foreach ($accounts as $account) {
            // Calculate balance for this account as of the date
            $debitSum = DB::table('journal_entries')
                ->where('account_id', $account->id)
                ->where('dt', '<=', $asOfDate)
                ->whereNotNull('debit')
                ->sum('debit');

            $creditSum = DB::table('journal_entries')
                ->where('account_id', $account->id)
                ->where('dt', '<=', $asOfDate)
                ->whereNotNull('credit')
                ->sum('credit');

            // Calculate balance based on account type
            // Assets and Expenses: Debit balance (Debit - Credit)
            // Liabilities, Equity, Revenue: Credit balance (Credit - Debit)
            $balance = ($balanceType === 'debit') 
                ? ($debitSum - $creditSum) 
                : ($creditSum - $debitSum);

            // Only include accounts with non-zero balances
            if ($balance != 0) {
                $breakdown[] = [
                    'account_id' => $account->id,
                    'account_name' => $account->name,
                    'amount' => $balance
                ];
                $total += $balance;
            }
        }

        // Sort breakdown by amount descending
        usort($breakdown, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Check if balance sheet is balanced (Assets = Liabilities + Equity).
     * Uses tolerance for floating point comparison.
     * 
     * @param float $assetsTotal
     * @param float $liabilitiesTotal
     * @param float $equitiesTotal
     * @return bool
     */
    private function isBalanced(float $assetsTotal, float $liabilitiesTotal, float $equitiesTotal): bool
    {
        $tolerance = 0.01; // 1 cent tolerance for rounding
        $difference = abs($assetsTotal - ($liabilitiesTotal + $equitiesTotal));
        
        return $difference <= $tolerance;
    }

    /**
     * Get monthly sales and profit data for the last N months.
     * Always returns the latest months ending with the current month.
     * OPTIMIZED: Uses single query per metric with GROUP BY for better performance.
     *
     * @param int $months Number of months to retrieve (default: 12, max: 36)
     * @return array Structured data for charting with labels, sales, and profit arrays
     */
    public function getMonthlySalesAndProfit(int $months = 12): array
    {
        // Ensure months is between 1 and 36
        $months = max(1, min(36, $months));

        // Get current date
        $endDate = Carbon::now();
        
        // Calculate start date (N months ago)
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        // OPTIMIZATION 1: Get all sales data in one query with GROUP BY
        $salesByMonth = Order::whereBetween('dt', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('YEAR(dt) as year'),
                DB::raw('MONTH(dt) as month'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy(DB::raw('YEAR(dt)'), DB::raw('MONTH(dt)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // OPTIMIZATION 2: Get all HPP/COGS data in one query with GROUP BY
        $hppByMonth = PurchaseOrder::whereBetween('dt', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('YEAR(dt) as year'),
                DB::raw('MONTH(dt) as month'),
                DB::raw('SUM(total) as total_hpp')
            )
            ->groupBy(DB::raw('YEAR(dt)'), DB::raw('MONTH(dt)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // OPTIMIZATION 3: Get all expenses data in one query with GROUP BY
        $expensesByMonth = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.dt', [$startDate, $endDate])
            ->whereNotNull('journal_entries.credit')
            ->where('accounts.id', 'like', '5%')
            ->select(
                DB::raw('YEAR(journal_entries.dt) as year'),
                DB::raw('MONTH(journal_entries.dt) as month'),
                DB::raw('SUM(journal_entries.credit) as total_expenses')
            )
            ->groupBy(DB::raw('YEAR(journal_entries.dt)'), DB::raw('MONTH(journal_entries.dt)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $labels = [];
        $salesData = [];
        $profitData = [];

        // Loop through each month from start to end, building arrays
        $currentMonth = $startDate->copy();
        
        while ($currentMonth <= $endDate) {
            $year = $currentMonth->year;
            $month = $currentMonth->month;
            $key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

            // Label format: "Nov 2024"
            $labels[] = $currentMonth->format('M Y');

            // Get values from pre-fetched data or 0 if not found
            $totalSales = isset($salesByMonth[$key]) 
                ? (float) $salesByMonth[$key]->total_sales 
                : 0.0;
            
            $totalHpp = isset($hppByMonth[$key]) 
                ? (float) $hppByMonth[$key]->total_hpp 
                : 0.0;
            
            $totalExpenses = isset($expensesByMonth[$key]) 
                ? (float) $expensesByMonth[$key]->total_expenses 
                : 0.0;

            // Calculate net profit: Sales - HPP - Expenses
            $netProfit = $totalSales - $totalHpp - $totalExpenses;

            // Round to 2 decimals and cast to float
            $salesData[] = round($totalSales, 2);
            $profitData[] = round($netProfit, 2);

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
    }    /**
     * Get monthly top products revenue data for the last N months.
     * Returns total revenue per month for bar chart visualization.
     * OPTIMIZED: Uses single query with GROUP BY for better performance.
     *
     * @param int $months Number of months to retrieve (default: 12, max: 36)
     * @return array Structured data with labels, revenues, and metadata
     */
    public function getMonthlyTopProducts(int $months = 12): array
    {
        // Ensure months is between 1 and 36
        $months = max(1, min(36, $months));

        // Calculate date range - always latest N months ending with current month
        $endDate = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        // OPTIMIZATION: Single query with GROUP BY instead of N queries
        $monthlyData = Order::whereBetween('dt', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('YEAR(dt) as year'),
                DB::raw('MONTH(dt) as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy(DB::raw('YEAR(dt)'), DB::raw('MONTH(dt)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $labels = [];
        $revenues = [];
        $monthsData = [];

        // Generate all months in range, filling gaps with 0
        $currentMonth = $startDate->copy();
        while ($currentMonth <= $endDate) {
            $year = $currentMonth->year;
            $month = $currentMonth->month;
            $key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            
            // Label format: "Jun 2025"
            $monthLabel = $currentMonth->format('M Y');
            $labels[] = $monthLabel;

            // Get revenue from query result or 0 if not found
            $monthRevenue = isset($monthlyData[$key]) 
                ? round((float) $monthlyData[$key]->revenue, 2)
                : 0.0;
            
            $revenues[] = $monthRevenue;

            // Store month data for reference
            $monthsData[] = [
                'year' => $year,
                'month' => $month,
                'label' => $monthLabel,
                'revenue' => $monthRevenue,
            ];

            // Move to next month
            $currentMonth->addMonth();
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'months_data' => $monthsData,
            'meta' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'months' => $months,
            ]
        ];
    }

    /**
     * Get top K products (units) for a specific year and month.
     * Returns products ordered by revenue descending.
     * 
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @param int $top Number of top products to return (default: 5, max: 20)
     * @return array Structured data with top products and month information
     */
    public function getTopProductsForMonth(int $year, int $month, int $top = 5): array
    {
        // Ensure top is between 1 and 20
        $top = max(1, min(20, $top));

        // Create Carbon date for the specified month
        $date = Carbon::createFromDate($year, $month, 1);
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();
        $monthLabel = $date->format('M Y');

        // Calculate total month revenue
        $totalMonthRevenue = Order::whereBetween('dt', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('total');

        // Get top products (units) by revenue
        // Join with units, clusters, projects to get product names
        $topProducts = DB::table('orders')
            ->join('units', 'orders.unit_id', '=', 'units.id')
            ->leftJoin('clusters', 'orders.cluster_id', '=', 'clusters.id')
            ->leftJoin('projects', 'orders.project_id', '=', 'projects.id')
            ->whereBetween('orders.dt', [$monthStart, $monthEnd])
            ->where('orders.status', 'completed')
            ->select(
                'orders.unit_id as product_id',
                DB::raw('CONCAT(projects.name, " - ", clusters.name, " - ", units.name) as product_name'),
                DB::raw('SUM(orders.total) as revenue')
            )
            ->groupBy('orders.unit_id', 'units.name', 'clusters.name', 'projects.name')
            ->orderByDesc('revenue')
            ->limit($top)
            ->get();

        // Format products data with percentage
        $formattedProducts = [];
        foreach ($topProducts as $index => $product) {
            $revenue = round((float) $product->revenue, 2);
            $percentage = $totalMonthRevenue > 0 
                ? round(($revenue / $totalMonthRevenue) * 100, 2) 
                : 0;

            $formattedProducts[] = [
                'rank' => $index + 1,
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'revenue' => $revenue,
                'percentage' => $percentage,
            ];
        }

        return [
            'year' => $year,
            'month' => $month,
            'label' => $monthLabel,
            'total_month_revenue' => round((float) $totalMonthRevenue, 2),
            'top' => $top,
            'top_products' => $formattedProducts,
        ];
    }

    /**
     * Get monthly summary comparing current month to previous month.
     * Returns revenue, purchase order count, and net profit with percent changes.
     *
     * @param \Carbon\Carbon|null $asOf Date to use as "current month" (defaults to today)
     * @return array Monthly summary with current vs previous comparison
     */
    public function getMonthlySummary(?\Carbon\Carbon $asOf = null): array
    {
        // Default to today if not specified
        $asOf = $asOf ?? Carbon::now();

        // Current month period
        $currentStart = $asOf->copy()->startOfMonth();
        $currentEnd = $asOf->copy()->endOfMonth();

        // Previous month period
        $previousStart = $asOf->copy()->subMonth()->startOfMonth();
        $previousEnd = $asOf->copy()->subMonth()->endOfMonth();

        // Calculate total revenue for current month (completed orders)
        $currentRevenue = Order::whereBetween('dt', [$currentStart, $currentEnd])
            ->where('status', 'completed')
            ->sum('total');

        // Calculate total revenue for previous month
        $previousRevenue = Order::whereBetween('dt', [$previousStart, $previousEnd])
            ->where('status', 'completed')
            ->sum('total');

        // Calculate purchase order counts
        $currentPoCount = PurchaseOrder::whereBetween('dt', [$currentStart, $currentEnd])
            ->count();

        $previousPoCount = PurchaseOrder::whereBetween('dt', [$previousStart, $previousEnd])
            ->count();

        // Calculate net profit using existing method
        $currentProfitLoss = $this->calculateProfitLoss(
            $currentStart->format('Y-m-d'),
            $currentEnd->format('Y-m-d')
        );
        $currentNetProfit = $currentProfitLoss['net_profit'];

        $previousProfitLoss = $this->calculateProfitLoss(
            $previousStart->format('Y-m-d'),
            $previousEnd->format('Y-m-d')
        );
        $previousNetProfit = $previousProfitLoss['net_profit'];

        // Helper function to calculate percent change
        $calculatePercentChange = function ($current, $previous) {
            if ($previous == 0) {
                // Handle division by zero
                return null;
            }
            return round((($current - $previous) / abs($previous)) * 100, 2);
        };

        return [
            'as_of' => $asOf->format('Y-m-d'),
            'current_period' => [
                'start' => $currentStart->format('Y-m-d'),
                'end' => $currentEnd->format('Y-m-d'),
            ],
            'previous_period' => [
                'start' => $previousStart->format('Y-m-d'),
                'end' => $previousEnd->format('Y-m-d'),
            ],
            'metrics' => [
                'total_revenue' => [
                    'current' => round((float) $currentRevenue, 2),
                    'previous' => round((float) $previousRevenue, 2),
                    'percent_change' => $calculatePercentChange($currentRevenue, $previousRevenue),
                ],
                'total_purchase_orders_count' => [
                    'current' => (int) $currentPoCount,
                    'previous' => (int) $previousPoCount,
                    'percent_change' => $calculatePercentChange($currentPoCount, $previousPoCount),
                ],
                'total_net_profit' => [
                    'current' => round((float) $currentNetProfit, 2),
                    'previous' => round((float) $previousNetProfit, 2),
                    'percent_change' => $calculatePercentChange($currentNetProfit, $previousNetProfit),
                ],
            ],
        ];
    }
}
