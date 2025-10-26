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
}

