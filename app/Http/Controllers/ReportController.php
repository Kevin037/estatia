<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Report service instance
     */
    protected $reportService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display Profit & Loss report
     */
    public function profitLoss(Request $request)
    {
        // Validate date inputs
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        // Default date range: start of current month to today
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Calculate profit & loss using service
        $data = $this->reportService->calculateProfitLoss($startDate, $endDate);

        return view('reports.profit_loss', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Display Balance Sheet report
     */
    public function balanceSheet(Request $request)
    {
        // Validate date inputs
        $request->validate([
            'as_of' => 'nullable|date|date_format:Y-m-d',
            'pl_start_date' => 'nullable|date|date_format:Y-m-d',
            'pl_end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:pl_start_date',
        ]);

        // Get as_of date (default to today)
        $asOfDate = $request->input('as_of', Carbon::now()->format('Y-m-d'));

        // Default P&L period: start of month of as_of -> as_of
        $plStartDate = $request->input('pl_start_date', Carbon::parse($asOfDate)->startOfMonth()->format('Y-m-d'));
        $plEndDate = $request->input('pl_end_date', $asOfDate);

        // Calculate balance sheet using service
        $data = $this->reportService->calculateBalanceSheet($asOfDate, $plStartDate, $plEndDate);

        return view('reports.balance_sheet', compact('data', 'asOfDate', 'plStartDate', 'plEndDate'));
    }

    /**
     * Get monthly sales and profit growth data (JSON API endpoint)
     */
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

    /**
     * Get monthly top products revenue data (JSON API endpoint)
     */
    public function monthlyTopProducts(Request $request)
    {
        // Validate months parameter
        $request->validate([
            'months' => 'nullable|integer|min:1|max:36',
        ]);

        // Get months parameter (default: 12)
        $months = $request->input('months', 12);

        // Get data from service
        $data = $this->reportService->getMonthlyTopProducts($months);

        // Return JSON response
        return response()->json($data);
    }

    /**
     * Get top products drilldown for a specific month (JSON API endpoint)
     */
    public function monthlyTopProductsDrilldown(int $year, int $month, Request $request)
    {
        // Validate input parameters
        $request->validate([
            'top' => 'nullable|integer|min:1|max:20',
        ]);

        // Validate year and month ranges
        if ($year < 2000 || $year > 2100) {
            return response()->json(['error' => 'Invalid year'], 400);
        }

        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid month'], 400);
        }

        // Get top parameter (default: 5)
        $top = $request->input('top', 5);

        // Get data from service
        $data = $this->reportService->getTopProductsForMonth($year, $month, $top);

        // Return JSON response
        return response()->json($data);
    }

    /**
     * Get monthly summary comparing current month to previous month (JSON API endpoint)
     */
    public function monthlySummary(Request $request)
    {
        // Optional: allow specifying as_of date
        $asOf = $request->has('as_of') 
            ? \Carbon\Carbon::parse($request->input('as_of'))
            : null;

        // Get data from service
        $data = $this->reportService->getMonthlySummary($asOf);

        // Return JSON response
        return response()->json($data);
    }
}


