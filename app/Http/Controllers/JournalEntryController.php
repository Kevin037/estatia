<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use Carbon\Carbon;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of journal entries grouped by transaction.
     */
    public function index(Request $request)
    {
        // Default date range: 1 month back
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));

        // Get journal entries within date range, ordered by ID DESC and grouped by journal_entry_id
        $journalEntries = JournalEntry::with('account')
            ->whereBetween('dt', [$startDate, $endDate])
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('journal_entry_id'); // Group by transaction ID

        return view('journal-entries.index', compact('journalEntries', 'startDate', 'endDate'));
    }
}

