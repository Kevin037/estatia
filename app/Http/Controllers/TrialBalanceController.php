<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Carbon\Carbon;

class TrialBalanceController extends Controller
{
    /**
     * Display the trial balance report.
     */
    public function index(Request $request)
    {
        // Default date range: 1 month back
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));

        // Get all accounts with parent_id not null, ordered by ID ASC
        $accounts = Account::whereNotNull('parent_id')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate balances for each account
        $trialBalanceData = [];
        $totals = [
            'saldo_awal_debit' => 0,
            'saldo_awal_kredit' => 0,
            'mutasi_debit' => 0,
            'mutasi_kredit' => 0,
            'saldo_akhir_debit' => 0,
            'saldo_akhir_kredit' => 0,
            'saldo_akhir' => 0,
        ];

        foreach ($accounts as $account) {
            // Saldo Awal Debit: journal_entries('debit', $dt_start)->sum('debit')
            $saldoAwalDebit = $account->journal_entries('debit', $startDate)->sum('debit');
            
            // Saldo Awal Kredit: journal_entries('credit', $dt_start)->sum('credit')
            $saldoAwalKredit = $account->journal_entries('credit', $startDate)->sum('credit');
            
            // Mutasi Debit: journal_entries('debit', $dt_start, $dt_end)->sum('debit')
            $mutasiDebit = $account->journal_entries('debit', $startDate, $endDate)->sum('debit');
            
            // Mutasi Kredit: journal_entries('credit', $dt_start, $dt_end)->sum('credit')
            $mutasiKredit = $account->journal_entries('credit', $startDate, $endDate)->sum('credit');
            
            // Saldo Akhir Debit: Saldo Awal Debit + Mutasi Debit
            $saldoAkhirDebit = $saldoAwalDebit + $mutasiDebit;
            
            // Saldo Akhir Kredit: Saldo Awal Kredit + Mutasi Kredit
            $saldoAkhirKredit = $saldoAwalKredit + $mutasiKredit;
            
            // Saldo Akhir: Saldo Akhir Debit - Saldo Akhir Kredit
            $saldoAkhir = $saldoAkhirDebit - $saldoAkhirKredit;

            $trialBalanceData[] = [
                'account' => $account,
                'saldo_awal_debit' => $saldoAwalDebit,
                'saldo_awal_kredit' => $saldoAwalKredit,
                'mutasi_debit' => $mutasiDebit,
                'mutasi_kredit' => $mutasiKredit,
                'saldo_akhir_debit' => $saldoAkhirDebit,
                'saldo_akhir_kredit' => $saldoAkhirKredit,
                'saldo_akhir' => $saldoAkhir,
            ];

            // Accumulate totals
            $totals['saldo_awal_debit'] += $saldoAwalDebit;
            $totals['saldo_awal_kredit'] += $saldoAwalKredit;
            $totals['mutasi_debit'] += $mutasiDebit;
            $totals['mutasi_kredit'] += $mutasiKredit;
            $totals['saldo_akhir_debit'] += $saldoAkhirDebit;
            $totals['saldo_akhir_kredit'] += $saldoAkhirKredit;
            $totals['saldo_akhir'] += $saldoAkhir;
        }

        return view('trial-balance.index', compact('trialBalanceData', 'totals', 'startDate', 'endDate'));
    }
}
