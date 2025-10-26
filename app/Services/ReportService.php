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
}
