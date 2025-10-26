<?php

use App\Models\JournalEntry;

echo "=== Journal Entry Balance Verification ===" . PHP_EOL . PHP_EOL;

$transactionTypes = ['Order', 'Payment', 'PurchaseOrder'];

foreach ($transactionTypes as $type) {
    $entries = JournalEntry::where('transaction_name', $type)->get();
    $debit = $entries->sum('debit');
    $credit = $entries->sum('credit');
    $balance = $debit - $credit;
    $count = $entries->count();
    
    echo "{$type}:" . PHP_EOL;
    echo "  Entries: {$count}" . PHP_EOL;
    echo "  Debit:   Rp " . number_format($debit, 2, ',', '.') . PHP_EOL;
    echo "  Credit:  Rp " . number_format($credit, 2, ',', '.') . PHP_EOL;
    echo "  Balance: Rp " . number_format($balance, 2, ',', '.') . " ";
    echo ($balance == 0 ? "✅" : "❌") . PHP_EOL . PHP_EOL;
}

echo "=== Overall Balance ===" . PHP_EOL;
$totalDebit = JournalEntry::sum('debit');
$totalCredit = JournalEntry::sum('credit');
$totalBalance = $totalDebit - $totalCredit;

echo "Total Debit:  Rp " . number_format($totalDebit, 2, ',', '.') . PHP_EOL;
echo "Total Credit: Rp " . number_format($totalCredit, 2, ',', '.') . PHP_EOL;
echo "Difference:   Rp " . number_format($totalBalance, 2, ',', '.') . " ";
echo ($totalBalance == 0 ? "✅ BALANCED" : "❌ NOT BALANCED") . PHP_EOL;
