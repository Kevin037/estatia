<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GeneralLedgerController extends Controller
{
    /**
     * Display a listing of accounts (Buku Besar)
     */
    public function index(Request $request)
    {
        // Default date range: one month back
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get accounts where parent_id is not null (child accounts only)
        $accounts = Account::whereNotNull('parent_id')
            ->orderBy('id', 'asc')
            ->get();

        return view('general-ledger.index', compact('accounts', 'startDate', 'endDate'));
    }

    /**
     * Display the specified account's general ledger detail
     */
    public function show(Request $request, Account $account)
    {
        // Get date range from request or use defaults
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get journal entries for this account within the date range
        $entries = JournalEntry::where('account_id', $account->id)
            ->whereBetween('dt', [$startDate, $endDate])
            ->orderBy('dt', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate running balance and totals
        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        $entriesWithBalance = $entries->map(function ($entry) use (&$runningBalance, &$totalDebit, &$totalCredit) {
            $runningBalance += $entry->debit - $entry->credit;
            $totalDebit += $entry->debit;
            $totalCredit += $entry->credit;

            $entry->running_balance = $runningBalance;
            return $entry;
        });

        return view('general-ledger.show', compact(
            'account',
            'entriesWithBalance',
            'startDate',
            'endDate',
            'totalDebit',
            'totalCredit',
            'runningBalance'
        ));
    }
}
