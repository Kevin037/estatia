# Eloquent Functions Documentation

## Overview
Two new eloquent functions have been added to enhance journal entry and account relationships.

---

## 1. JournalEntry Model - `transaction()` Function

### Location
`app/Models/JournalEntry.php`

### Purpose
Dynamically retrieve the related transaction record based on `transaction_id` and `transaction_name`.

### How It Works
- Reads the `transaction_name` field (e.g., 'Order', 'PurchaseOrder', 'Payment')
- Converts it to the corresponding model class (e.g., `App\Models\Order`)
- Retrieves the record from that table using `transaction_id`

### Signature
```php
public function transaction()
```

### Returns
- Returns the related model instance (Order, PurchaseOrder, Payment, etc.)
- Returns `null` if transaction not found or model class doesn't exist

### Usage Examples

#### Example 1: Get Related Purchase Order
```php
$journalEntry = JournalEntry::find(1);

// Get the related transaction
$purchaseOrder = $journalEntry->transaction();

if ($purchaseOrder) {
    echo "PO Number: " . $purchaseOrder->no;
    echo "Total: " . $purchaseOrder->total;
}
```

#### Example 2: Get Related Order with Customer
```php
$entry = JournalEntry::where('transaction_name', 'Order')
    ->where('transaction_id', 20)
    ->first();

$order = $entry->transaction();

if ($order) {
    echo "Customer: " . $order->customer->name;
    echo "Project: " . $order->project->name;
}
```

#### Example 3: Loop Through Journal Entries
```php
$entries = JournalEntry::where('dt', '2024-12-31')->get();

foreach ($entries as $entry) {
    $transaction = $entry->transaction();
    
    if ($transaction) {
        echo "Entry #{$entry->id} relates to {$entry->transaction_name} #{$transaction->id}\n";
    }
}
```

#### Example 4: Display Journal Entry with Transaction Details
```php
$journalEntries = JournalEntry::with('account')
    ->where('journal_entry_id', 1)
    ->get();

foreach ($journalEntries as $entry) {
    echo "Account: {$entry->account->name}\n";
    echo "Debit: {$entry->debit}\n";
    echo "Credit: {$entry->credit}\n";
    
    $transaction = $entry->transaction();
    if ($transaction) {
        echo "Related to: {$entry->transaction_name} ";
        echo isset($transaction->no) ? "#{$transaction->no}" : "ID #{$transaction->id}";
    }
    echo "\n---\n";
}
```

### Supported Transaction Types
The function works with any model name stored in `transaction_name`:
- `Order` → `App\Models\Order`
- `PurchaseOrder` → `App\Models\PurchaseOrder`
- `Payment` → `App\Models\Payment`
- `Invoice` → `App\Models\Invoice`
- `Production` → `App\Models\Production`
- Any other model following the naming convention

---

## 2. Account Model - `journal_entries()` Function

### Location
`app/Models/Account.php`

### Purpose
Get journal entries for an account with specific filters for entry type and date range.

### Signature
```php
public function journal_entries($type, $dt_start, $dt_end = null)
```

### Parameters
- **`$type`** (string): Column name to check for NOT NULL
  - Common values: `'debit'`, `'credit'`
  - Can be any column name in the journal_entries table
  
- **`$dt_start`** (string): Start date in 'Y-m-d' format
  - If `$dt_end` is null: Returns entries **before** this date (`dt < $dt_start`)
  - If `$dt_end` is provided: Returns entries **from** this date
  
- **`$dt_end`** (string|null): End date in 'Y-m-d' format (optional)
  - If null: Returns entries before `$dt_start`
  - If provided: Returns entries between `$dt_start` and `$dt_end`

### Returns
- Returns a query builder instance (not executed)
- Can chain additional methods: `->get()`, `->sum()`, `->count()`, etc.

### Usage Examples

#### Example 1: Get All Debit Entries Before a Date
```php
$account = Account::where('code', '101000000')->first(); // Kas

// Get all debit entries before Dec 31, 2024
$debitEntries = $account->journal_entries('debit', '2024-12-31')->get();

foreach ($debitEntries as $entry) {
    echo "Date: {$entry->dt}, Amount: {$entry->debit}\n";
}
```

#### Example 2: Get Credit Entries in Date Range
```php
$account = Account::where('code', '102000000')->first(); // Piutang Usaha

// Get credit entries for the year 2024
$creditEntries = $account->journal_entries('credit', '2024-01-01', '2024-12-31')->get();

echo "Total entries: " . $creditEntries->count();
```

#### Example 3: Calculate Account Balance
```php
$account = Account::where('code', '101000000')->first(); // Kas

$startDate = '2024-01-01';
$endDate = '2024-12-31';

// Get total debits
$totalDebit = $account->journal_entries('debit', $startDate, $endDate)
    ->sum('debit');

// Get total credits
$totalCredit = $account->journal_entries('credit', $startDate, $endDate)
    ->sum('credit');

// Calculate balance
$balance = $totalDebit - $totalCredit;

echo "Account Balance for 2024: Rp " . number_format($balance, 0, ',', '.');
```

#### Example 4: Get Opening Balance (Entries Before Start Date)
```php
$account = Account::where('code', '103001000')->first(); // Inventory

$reportStartDate = '2024-01-01';

// Get opening balance (all entries before report start date)
$openingDebit = $account->journal_entries('debit', $reportStartDate)
    ->sum('debit');

$openingCredit = $account->journal_entries('credit', $reportStartDate)
    ->sum('credit');

$openingBalance = $openingDebit - $openingCredit;

echo "Opening Balance: Rp " . number_format($openingBalance, 0, ',', '.');
```

#### Example 5: Monthly Report with Transaction Details
```php
$account = Account::where('code', '201002000')->first(); // Utang Bank

$startDate = '2024-12-01';
$endDate = '2024-12-31';

// Get all entries for December 2024
$entries = $account->journal_entries('debit', $startDate, $endDate)
    ->orWhere(function($q) use ($startDate, $endDate) {
        $q->whereNotNull('credit')
          ->whereBetween('dt', [$startDate, $endDate]);
    })
    ->orderBy('dt')
    ->get();

foreach ($entries as $entry) {
    echo "Date: {$entry->dt}\n";
    echo "Description: {$entry->desc}\n";
    echo "Debit: " . number_format($entry->debit, 0, ',', '.') . "\n";
    echo "Credit: " . number_format($entry->credit, 0, ',', '.') . "\n";
    
    // Get related transaction
    $transaction = $entry->transaction();
    if ($transaction) {
        echo "Related: {$entry->transaction_name}";
        echo isset($transaction->no) ? " #{$transaction->no}" : " ID #{$transaction->id}";
    }
    echo "\n---\n";
}
```

#### Example 6: Generate Trial Balance
```php
$accounts = Account::all();
$startDate = '2024-01-01';
$endDate = '2024-12-31';

echo "TRIAL BALANCE - Year 2024\n";
echo str_repeat('=', 80) . "\n";
printf("%-40s %15s %15s\n", "Account", "Debit", "Credit");
echo str_repeat('-', 80) . "\n";

$totalDebit = 0;
$totalCredit = 0;

foreach ($accounts as $account) {
    $debit = $account->journal_entries('debit', $startDate, $endDate)->sum('debit');
    $credit = $account->journal_entries('credit', $startDate, $endDate)->sum('credit');
    
    if ($debit > 0 || $credit > 0) {
        printf(
            "%-40s %15s %15s\n",
            $account->code . ' - ' . $account->name,
            number_format($debit, 0, ',', '.'),
            number_format($credit, 0, ',', '.')
        );
        
        $totalDebit += $debit;
        $totalCredit += $credit;
    }
}

echo str_repeat('-', 80) . "\n";
printf("%-40s %15s %15s\n", "TOTAL", number_format($totalDebit, 0, ',', '.'), number_format($totalCredit, 0, ',', '.'));
echo str_repeat('=', 80) . "\n";
```

#### Example 7: Account Statement
```php
$account = Account::where('code', '102000000')->first(); // Piutang Usaha

$startDate = '2024-01-01';
$endDate = '2024-12-31';

// Calculate opening balance
$openingDebit = $account->journal_entries('debit', $startDate)->sum('debit');
$openingCredit = $account->journal_entries('credit', $startDate)->sum('credit');
$openingBalance = $openingDebit - $openingCredit;

echo "ACCOUNT STATEMENT\n";
echo "Account: {$account->code} - {$account->name}\n";
echo "Period: {$startDate} to {$endDate}\n";
echo str_repeat('=', 100) . "\n";
echo "Opening Balance: Rp " . number_format($openingBalance, 0, ',', '.') . "\n";
echo str_repeat('-', 100) . "\n";

// Get all entries in period
$allEntries = JournalEntry::where('account_id', $account->id)
    ->whereBetween('dt', [$startDate, $endDate])
    ->orderBy('dt')
    ->get();

$runningBalance = $openingBalance;

foreach ($allEntries as $entry) {
    $runningBalance += $entry->debit - $entry->credit;
    
    printf(
        "%-12s %-30s %15s %15s %15s\n",
        $entry->dt->format('Y-m-d'),
        substr($entry->desc ?? '', 0, 30),
        number_format($entry->debit, 0, ',', '.'),
        number_format($entry->credit, 0, ',', '.'),
        number_format($runningBalance, 0, ',', '.')
    );
}

echo str_repeat('-', 100) . "\n";
echo "Closing Balance: Rp " . number_format($runningBalance, 0, ',', '.') . "\n";
```

---

## Practical Use Cases

### Use Case 1: Audit Trail
```php
// Show complete audit trail for a specific transaction
$purchaseOrderId = 123;

$entries = JournalEntry::where('transaction_id', $purchaseOrderId)
    ->where('transaction_name', 'PurchaseOrder')
    ->with('account')
    ->get();

echo "Audit Trail for Purchase Order #{$purchaseOrderId}\n";
echo str_repeat('=', 80) . "\n";

foreach ($entries as $entry) {
    echo "Date: {$entry->dt}\n";
    echo "Account: {$entry->account->code} - {$entry->account->name}\n";
    echo "Debit: Rp " . number_format($entry->debit, 0, ',', '.') . "\n";
    echo "Credit: Rp " . number_format($entry->credit, 0, ',', '.') . "\n";
    echo "Description: {$entry->desc}\n";
    echo str_repeat('-', 80) . "\n";
}
```

### Use Case 2: Cash Flow Report
```php
$cashAccount = Account::where('code', '101000000')->first();
$startDate = '2024-01-01';
$endDate = '2024-12-31';

$cashIn = $cashAccount->journal_entries('debit', $startDate, $endDate)->sum('debit');
$cashOut = $cashAccount->journal_entries('credit', $startDate, $endDate)->sum('credit');
$netCashFlow = $cashIn - $cashOut;

echo "CASH FLOW REPORT\n";
echo "Period: {$startDate} to {$endDate}\n";
echo str_repeat('=', 50) . "\n";
echo "Cash Inflow:  Rp " . number_format($cashIn, 0, ',', '.') . "\n";
echo "Cash Outflow: Rp " . number_format($cashOut, 0, ',', '.') . "\n";
echo str_repeat('-', 50) . "\n";
echo "Net Cash Flow: Rp " . number_format($netCashFlow, 0, ',', '.') . "\n";
```

### Use Case 3: Inventory Valuation
```php
$rawMaterialAccount = Account::where('code', '103001000')->first();
$wipAccount = Account::where('code', '103002000')->first();
$finishedGoodsAccount = Account::where('code', '103003000')->first();

$asOfDate = date('Y-m-d');

// Calculate current inventory values
$rawMaterialValue = $rawMaterialAccount->journal_entries('debit', $asOfDate)->sum('debit')
    - $rawMaterialAccount->journal_entries('credit', $asOfDate)->sum('credit');

$wipValue = $wipAccount->journal_entries('debit', $asOfDate)->sum('debit')
    - $wipAccount->journal_entries('credit', $asOfDate)->sum('credit');

$finishedGoodsValue = $finishedGoodsAccount->journal_entries('debit', $asOfDate)->sum('debit')
    - $finishedGoodsAccount->journal_entries('credit', $asOfDate)->sum('credit');

$totalInventory = $rawMaterialValue + $wipValue + $finishedGoodsValue;

echo "INVENTORY VALUATION - As of {$asOfDate}\n";
echo str_repeat('=', 60) . "\n";
echo "Raw Materials:    Rp " . number_format($rawMaterialValue, 0, ',', '.') . "\n";
echo "Work in Process:  Rp " . number_format($wipValue, 0, ',', '.') . "\n";
echo "Finished Goods:   Rp " . number_format($finishedGoodsValue, 0, ',', '.') . "\n";
echo str_repeat('-', 60) . "\n";
echo "Total Inventory:  Rp " . number_format($totalInventory, 0, ',', '.') . "\n";
```

---

## Important Notes

### Note 1: Query Builder vs Collection
The `journal_entries()` function returns a **query builder**, not a collection. This allows you to:
- Chain additional query methods
- Optimize performance by adding filters
- Use aggregation functions (sum, count, avg, etc.)

```php
// ✅ Good - Using query builder methods
$total = $account->journal_entries('debit', '2024-01-01', '2024-12-31')
    ->where('desc', 'like', '%salary%')
    ->sum('debit');

// ✅ Good - Get collection when needed
$entries = $account->journal_entries('credit', '2024-01-01')->get();
```

### Note 2: Date Format
Always use 'Y-m-d' format for dates:
```php
// ✅ Correct
$entries = $account->journal_entries('debit', '2024-12-31')->get();

// ❌ Wrong
$entries = $account->journal_entries('debit', '31-12-2024')->get();
```

### Note 3: Transaction Function Returns Instance
The `transaction()` function returns a **model instance**, not a relationship:
```php
// ✅ Correct
$transaction = $entry->transaction();
if ($transaction) {
    echo $transaction->no;
}

// ❌ Wrong - Cannot use as Eloquent relationship
$entry->with('transaction')->get(); // This won't work
```

---

## Testing Commands

### Test JournalEntry->transaction()
```php
php artisan tinker

$entry = JournalEntry::first();
$transaction = $entry->transaction();
dd($transaction);
```

### Test Account->journal_entries()
```php
php artisan tinker

$account = Account::where('code', '101000000')->first();
$entries = $account->journal_entries('debit', '2024-12-31')->get();
dd($entries);
```

---

## Files Modified

1. ✅ `app/Models/JournalEntry.php` - Added `transaction()` function
2. ✅ `app/Models/Account.php` - Added `journal_entries($type, $dt_start, $dt_end)` function

---

## Summary

These two eloquent functions provide powerful tools for:
- **Tracking transactions** through journal entries
- **Generating financial reports** (trial balance, account statements, etc.)
- **Calculating balances** for any time period
- **Auditing** business processes
- **Analyzing cash flow** and inventory valuation

Both functions have been tested and are ready to use in your controllers, reports, and views!
