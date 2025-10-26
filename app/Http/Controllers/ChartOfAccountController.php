<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of all accounts (Chart of Account).
     */
    public function index()
    {
        // Get all accounts ordered by ID
        $accounts = Account::with('parent')
            ->orderBy('id', 'asc')
            ->get();

        return view('chart-of-accounts.index', compact('accounts'));
    }
}
