# Eloquent Functions Quick Reference

## ✅ Implementation Complete

### 1. JournalEntry Model - `transaction()` Function

**Purpose:** Get the related transaction record dynamically

**Location:** `app/Models/JournalEntry.php`

**Usage:**
```php
$journalEntry = JournalEntry::find(1);
$transaction = $journalEntry->transaction();

// Returns: Order, PurchaseOrder, Payment, Invoice, Production, etc.
```

**How it works:**
- Reads `transaction_name` (e.g., 'Order', 'PurchaseOrder')
- Reads `transaction_id` (e.g., 20)
- Converts to model class: `App\Models\Order`
- Returns: `Order::find(20)`

**Example:**
```php
$entry = JournalEntry::where('transaction_name', 'Order')
    ->where('transaction_id', 20)
    ->first();

$order = $entry->transaction();
echo $order->no; // Output: ORD-000020
echo $order->customer->name; // Output: John Doe
```

---

### 2. Account Model - `journal_entries()` Function

**Purpose:** Get filtered journal entries for an account

**Location:** `app/Models/Account.php`

**Signature:**
```php
journal_entries($type, $dt_start, $dt_end = null)
```

**Parameters:**
- `$type`: Column to check for NOT NULL ('debit' or 'credit')
- `$dt_start`: Start date ('Y-m-d' format)
- `$dt_end`: End date (optional, 'Y-m-d' format)

**Behavior:**
- If `$dt_end` is **null**: Returns entries **before** `$dt_start` (`dt < $dt_start`)
- If `$dt_end` is **provided**: Returns entries **between** dates (`whereBetween`)

**Usage Examples:**

#### Get debit entries before a date:
```php
$account = Account::where('code', '101000000')->first();
$entries = $account->journal_entries('debit', '2024-12-31')->get();
// Returns all debit entries before Dec 31, 2024
```

#### Get credit entries in date range:
```php
$account = Account::where('code', '102000000')->first();
$entries = $account->journal_entries('credit', '2024-01-01', '2024-12-31')->get();
// Returns credit entries from Jan 1 to Dec 31, 2024
```

#### Calculate balance:
```php
$account = Account::where('code', '101000000')->first();
$debit = $account->journal_entries('debit', '2024-01-01', '2024-12-31')->sum('debit');
$credit = $account->journal_entries('credit', '2024-01-01', '2024-12-31')->sum('credit');
$balance = $debit - $credit;
```

---

## Common Use Cases

### 1. Show Transaction Details from Journal Entry
```php
$entry = JournalEntry::find(1);
$transaction = $entry->transaction();

if ($transaction) {
    echo "Related to: {$entry->transaction_name} #{$transaction->no}";
}
```

### 2. Calculate Opening Balance
```php
$account = Account::where('code', '101000000')->first();
$reportDate = '2024-01-01';

$openingDebit = $account->journal_entries('debit', $reportDate)->sum('debit');
$openingCredit = $account->journal_entries('credit', $reportDate)->sum('credit');
$openingBalance = $openingDebit - $openingCredit;
```

### 3. Monthly Account Statement
```php
$account = Account::where('code', '102000000')->first();
$startDate = '2024-12-01';
$endDate = '2024-12-31';

$entries = $account->journal_entries('debit', $startDate, $endDate)
    ->get();

foreach ($entries as $entry) {
    $transaction = $entry->transaction();
    echo "{$entry->dt}: {$entry->desc} - Rp {$entry->debit}";
    if ($transaction) {
        echo " (Ref: {$entry->transaction_name} #{$transaction->id})";
    }
    echo "\n";
}
```

### 4. Trial Balance Report
```php
$accounts = Account::all();
$startDate = '2024-01-01';
$endDate = '2024-12-31';

foreach ($accounts as $account) {
    $debit = $account->journal_entries('debit', $startDate, $endDate)->sum('debit');
    $credit = $account->journal_entries('credit', $startDate, $endDate)->sum('credit');
    
    if ($debit > 0 || $credit > 0) {
        echo "{$account->code} - {$account->name}\n";
        echo "Debit: Rp " . number_format($debit, 0, ',', '.') . "\n";
        echo "Credit: Rp " . number_format($credit, 0, ',', '.') . "\n";
    }
}
```

---

## Important Notes

### ⚠️ transaction() Returns Instance, Not Relationship
```php
// ✅ Correct
$transaction = $entry->transaction();

// ❌ Wrong - Cannot eager load
$entry->with('transaction')->get();
```

### ⚠️ journal_entries() Returns Query Builder
```php
// ✅ Correct - Chain methods before get()
$entries = $account->journal_entries('debit', '2024-01-01', '2024-12-31')
    ->where('desc', 'like', '%salary%')
    ->get();

// ✅ Correct - Use aggregation without get()
$total = $account->journal_entries('debit', '2024-01-01', '2024-12-31')
    ->sum('debit');
```

### ⚠️ Date Format Must Be 'Y-m-d'
```php
// ✅ Correct
$entries = $account->journal_entries('debit', '2024-12-31')->get();

// ❌ Wrong
$entries = $account->journal_entries('debit', '31/12/2024')->get();
```

---

## Testing

### Test in Tinker
```bash
php artisan tinker
```

```php
// Test transaction()
$entry = JournalEntry::first();
$transaction = $entry->transaction();
dd($transaction);

// Test journal_entries()
$account = Account::where('code', '101000000')->first();
$entries = $account->journal_entries('debit', '2025-01-01')->get();
dd($entries);
```

---

## Files Modified

| File | Function Added | Status |
|------|----------------|--------|
| `app/Models/JournalEntry.php` | `transaction()` | ✅ Complete |
| `app/Models/Account.php` | `journal_entries($type, $dt_start, $dt_end)` | ✅ Complete |

---

## Documentation

📖 **Full Documentation:** See `ELOQUENT_FUNCTIONS_DOCUMENTATION.md` for:
- Detailed explanations
- Advanced usage examples
- Financial reporting examples
- Best practices

---

## Summary

✅ **JournalEntry->transaction()**: Get related Order/PurchaseOrder/Payment/etc dynamically

✅ **Account->journal_entries()**: Get filtered entries by type and date range

Both functions tested and ready to use! 🎉
