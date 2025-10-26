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
}
