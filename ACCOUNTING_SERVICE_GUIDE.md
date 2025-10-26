# Accounting Service Integration Guide

## Overview
The `AccountingService` automatically creates journal entries for business transactions following double-entry bookkeeping principles.

## Service Location
`app/Services/AccountingService.php`

## Accounting Rules Implemented

### 1. Purchase Order Created
**Trigger:** When a purchase order is first created
**Journal Entry:**
- Debit: 103001000 (Persediaan Barang Dagang - Raw Material Inventory)
- Credit: 201002000 (Utang Bank - Supplier Payable)

### 2. Goods Received
**Trigger:** When purchase order status changes to 'completed'
**Journal Entry:**
- Debit: 103002000 (Persediaan Barang Bahan - Work in Process)
- Credit: 103001000 (Persediaan Barang Dagang - Raw Material Inventory)

### 3. Production Process
**Trigger:** When production is created
**Journal Entry:**
- Debit: 103003000 (Persediaan Barang Jadi - Finished Goods Inventory)
- Credit: 103002000 (Persediaan Barang Bahan - Work in Process)

### 4. Sales Order Created
**Trigger:** When a sales order is created
**Journal Entry:**
- Debit: 102000000 (Piutang Usaha - Accounts Receivable)
- Credit: 401001000 (Pendapatan Jasa - Sales Revenue)

### 5. Invoice Sent
**Trigger:** When an invoice is created/sent
**Journal Entry:**
- Debit: 102000000 (Piutang Usaha - Accounts Receivable)
- Credit: 401001000 (Pendapatan Jasa - Sales Revenue)
- Credit: 202001000 (PPN Keluaran - Output Tax)

### 6. Payment Received
**Trigger:** When payment is received from customer
**Journal Entry:**
- Debit: 101000000 (Kas - Cash)
- Credit: 102000000 (Piutang Usaha - Accounts Receivable)
- Credit: 202001000 (PPN Keluaran - Output Tax)

### 7. Payment to Supplier
**Trigger:** When purchase order status changes to 'completed' (same time as Goods Received)
**Journal Entry:**
- Debit: 201002000 (Utang Bank - Supplier Payable)
- Credit: 101000000 (Kas - Cash)

---

## Integration Instructions

### 1. PurchaseOrderController Integration

Add to the controller:

```php
<?php

namespace App\Http\Controllers;

use App\Services\AccountingService;
// ... other imports

class PurchaseOrderController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    // In store() method - AFTER creating the purchase order
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ... create purchase order code ...
            
            // Record accounting entry
            $this->accountingService->recordPurchaseOrderCreated(
                $purchaseOrder->id,
                $total,
                $request->dt
            );
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }

    // In update() method - WHEN status changes to 'completed'
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->status;
            
            // ... update purchase order code ...
            
            // If status changed to completed
            if ($oldStatus !== 'completed' && $purchaseOrder->status === 'completed') {
                // Record goods received
                $this->accountingService->recordGoodsReceived(
                    $purchaseOrder->id,
                    $total,
                    $request->dt
                );
                
                // Record supplier payment
                $this->accountingService->recordSupplierPayment(
                    $purchaseOrder->id,
                    $total,
                    $request->dt
                );
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }
}
```

### 2. Production Controller Integration

**Note:** You may need to create a ProductionController if it doesn't exist.

```php
<?php

namespace App\Http\Controllers;

use App\Services\AccountingService;

class ProductionController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ... create production record ...
            
            // Record accounting entry
            $this->accountingService->recordProductionProcess(
                $production->id,
                $amount,
                $request->date
            );
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }
}
```

### 3. OrderController Integration

```php
<?php

namespace App\Http\Controllers;

use App\Services\AccountingService;

class OrderController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ... create order code ...
            
            // Record accounting entry (without tax)
            $this->accountingService->recordSalesOrder(
                $order->id,
                $total,
                $request->dt
            );
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }
}
```

### 4. InvoiceController Integration

```php
<?php

namespace App\Http\Controllers;

use App\Services\AccountingService;

class InvoiceController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ... create invoice code ...
            
            // Calculate tax (e.g., 11% PPN)
            $subtotal = $invoice->order->total;
            $taxAmount = $subtotal * 0.11;
            
            // Record accounting entry
            $this->accountingService->recordInvoiceSent(
                $invoice->id,
                $subtotal,
                $taxAmount,
                $request->dt
            );
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }
}
```

### 5. PaymentController Integration

```php
<?php

namespace App\Http\Controllers;

use App\Services\AccountingService;

class PaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ... create payment code ...
            
            // Calculate tax amount
            $totalAmount = $payment->amount;
            $taxAmount = $totalAmount * 0.11 / 1.11; // Extract tax from total
            
            // Record accounting entry
            $this->accountingService->recordCustomerPayment(
                $payment->id,
                $totalAmount,
                $taxAmount,
                $request->dt
            );
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // handle error
        }
    }
}
```

---

## Important Notes

### Transaction Wrapping
- Always wrap accounting service calls within the same `DB::transaction()` as the main operation
- If the main operation fails, journal entries will be rolled back automatically

### Error Handling
- The service methods return `bool` (true/false)
- Check the return value and handle failures appropriately
- Errors are logged automatically to Laravel's log

### Deleting Transactions
If you need to delete a transaction and its journal entries:

```php
// Delete associated journal entries first
$this->accountingService->deleteJournalEntries(
    $transactionId,
    'TransactionName' // e.g., 'PurchaseOrder', 'Order', 'Payment', etc.
);

// Then delete the transaction
$transaction->delete();
```

### Journal Entry Structure
Each journal entry contains:
- `transaction_id`: ID of the related record (e.g., purchase_order_id)
- `transaction_name`: Type of transaction (e.g., 'PurchaseOrder', 'Order')
- `dt`: Transaction date
- `account_id`: Foreign key to accounts table
- `debit`: Debit amount
- `credit`: Credit amount
- `desc`: Description of the entry
- `journal_entry_id`: Groups related entries together

### Verification
To verify journal entries for a transaction:

```php
$entries = JournalEntry::where('transaction_id', $id)
    ->where('transaction_name', 'PurchaseOrder')
    ->with('account')
    ->get();
```

---

## Testing Checklist

- [ ] Purchase Order created → Journal entries recorded
- [ ] Purchase Order status changed to completed → Goods Received + Supplier Payment recorded
- [ ] Production created → Journal entries recorded
- [ ] Sales Order created → Journal entries recorded
- [ ] Invoice sent → Journal entries recorded
- [ ] Payment received → Journal entries recorded
- [ ] All debits equal credits for each transaction
- [ ] Deleting transactions also removes journal entries
- [ ] Errors are handled gracefully and logged

---

## Account Codes Reference

| Code | Account Name | Type |
|------|--------------|------|
| 101000000 | Kas | Asset |
| 102000000 | Piutang Usaha | Asset |
| 103001000 | Persediaan Barang Dagang | Asset |
| 103002000 | Persediaan Barang Bahan | Asset |
| 103003000 | Persediaan Barang Jadi | Asset |
| 201002000 | Utang Bank | Liability |
| 202001000 | PPN Keluaran | Liability |
| 401001000 | Pendapatan Jasa | Revenue |

---

## Support
For questions or issues, check the Laravel log files at `storage/logs/laravel.log`
