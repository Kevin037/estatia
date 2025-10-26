# Accounting Service Flow Diagram

## Process Flow Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                    BUSINESS PROCESS FLOW                             │
└─────────────────────────────────────────────────────────────────────┘

1. PURCHASE ORDER FLOW
═══════════════════════════════════════════════════════════════════════

   [Purchase Order Created]
           │
           ├─► Store in DB
           │
           └─► AccountingService.recordPurchaseOrderCreated()
                   │
                   ├─ Debit: 103001000 (Raw Material Inventory)
                   └─ Credit: 201002000 (Supplier Payable)

   ────────────────────────────────────────────────────────────────────

   [Purchase Order Status → Completed]
           │
           ├─► Update Status in DB
           │
           ├─► AccountingService.recordGoodsReceived()
           │       │
           │       ├─ Debit: 103002000 (Work in Process)
           │       └─ Credit: 103001000 (Raw Material)
           │
           └─► AccountingService.recordSupplierPayment()
                   │
                   ├─ Debit: 201002000 (Supplier Payable)
                   └─ Credit: 101000000 (Cash)


2. PRODUCTION FLOW
═══════════════════════════════════════════════════════════════════════

   [Production Created]
           │
           ├─► Store in DB
           │
           └─► AccountingService.recordProductionProcess()
                   │
                   ├─ Debit: 103003000 (Finished Goods)
                   └─ Credit: 103002000 (Work in Process)


3. SALES ORDER FLOW
═══════════════════════════════════════════════════════════════════════

   [Sales Order Created]
           │
           ├─► Store in DB
           │
           └─► AccountingService.recordSalesOrder()
                   │
                   ├─ Debit: 102000000 (Accounts Receivable)
                   └─ Credit: 401001000 (Sales Revenue)


4. INVOICE FLOW
═══════════════════════════════════════════════════════════════════════

   [Invoice Created/Sent]
           │
           ├─► Store in DB
           │
           └─► AccountingService.recordInvoiceSent()
                   │
                   ├─ Debit: 102000000 (Accounts Receivable)
                   ├─ Credit: 401001000 (Sales Revenue)
                   └─ Credit: 202001000 (Output Tax)


5. PAYMENT FLOW
═══════════════════════════════════════════════════════════════════════

   [Payment Received]
           │
           ├─► Store in DB
           │
           └─► AccountingService.recordCustomerPayment()
                   │
                   ├─ Debit: 101000000 (Cash)
                   ├─ Credit: 102000000 (Accounts Receivable)
                   └─ Credit: 202001000 (Output Tax)
```

---

## Account Relationships

```
┌────────────────────────────────────────────────────────────────┐
│                    CHART OF ACCOUNTS                            │
└────────────────────────────────────────────────────────────────┘

ASSETS (100000000)
├── 101000000 - Kas (Cash)
│   ├─► Used in: Payment Received, Payment to Supplier
│   └─► Balance: Increases with receipts, decreases with payments
│
├── 102000000 - Piutang Usaha (Accounts Receivable)
│   ├─► Used in: Sales Order, Invoice, Payment Received
│   └─► Balance: Increases with sales, decreases with payments
│
└── 103000000 - Persediaan (Inventory)
    ├── 103001000 - Barang Dagang (Raw Materials)
    │   ├─► Used in: Purchase Order, Goods Received
    │   └─► Flow: Purchase → WIP
    │
    ├── 103002000 - Barang Bahan (Work in Process)
    │   ├─► Used in: Goods Received, Production
    │   └─► Flow: Raw Materials → Finished Goods
    │
    └── 103003000 - Barang Jadi (Finished Goods)
        ├─► Used in: Production
        └─► Flow: WIP → Ready for Sale

LIABILITIES (200000000)
├── 201002000 - Utang Bank (Supplier Payable)
│   ├─► Used in: Purchase Order, Payment to Supplier
│   └─► Balance: Increases with purchases, decreases with payments
│
└── 202001000 - PPN Keluaran (Output Tax)
    ├─► Used in: Invoice, Payment Received
    └─► Balance: Tax liability to government

REVENUE (400000000)
└── 401001000 - Pendapatan Jasa (Sales Revenue)
    ├─► Used in: Sales Order, Invoice
    └─► Balance: Increases with each sale
```

---

## Integration Points

```
┌─────────────────────────────────────────────────────────────────┐
│              CONTROLLER INTEGRATION MAP                          │
└─────────────────────────────────────────────────────────────────┘

PurchaseOrderController
├── store()
│   └─► recordPurchaseOrderCreated($id, $total, $date)
│
└── update()
    └─► if (status changed to 'completed')
        ├─► recordGoodsReceived($id, $total, $date)
        └─► recordSupplierPayment($id, $total, $date)

────────────────────────────────────────────────────────────────────

ProductionController
└── store()
    └─► recordProductionProcess($id, $amount, $date)

────────────────────────────────────────────────────────────────────

OrderController
└── store()
    └─► recordSalesOrder($id, $total, $date)

────────────────────────────────────────────────────────────────────

InvoiceController
└── store()
    └─► recordInvoiceSent($id, $subtotal, $taxAmount, $date)

────────────────────────────────────────────────────────────────────

PaymentController
└── store()
    └─► recordCustomerPayment($id, $totalAmount, $taxAmount, $date)
```

---

## Data Flow Example

```
EXAMPLE: Complete Purchase Order to Payment Flow

Step 1: Purchase Order Created (PO-001, Rp 10,000,000)
┌─────────────────────────────────────────────────────────┐
│ Dr. 103001000 (Raw Material)        Rp 10,000,000      │
│ Cr. 201002000 (Supplier Payable)    Rp 10,000,000      │
└─────────────────────────────────────────────────────────┘
Result: Raw material added, debt recorded

Step 2: Goods Received (PO-001 Status → Completed)
┌─────────────────────────────────────────────────────────┐
│ Dr. 103002000 (Work in Process)     Rp 10,000,000      │
│ Cr. 103001000 (Raw Material)        Rp 10,000,000      │
└─────────────────────────────────────────────────────────┘
Result: Materials moved to production

Step 3: Payment to Supplier (PO-001 Completed)
┌─────────────────────────────────────────────────────────┐
│ Dr. 201002000 (Supplier Payable)    Rp 10,000,000      │
│ Cr. 101000000 (Cash)                Rp 10,000,000      │
└─────────────────────────────────────────────────────────┘
Result: Debt cleared, cash reduced

Step 4: Production Completed (PROD-001, Rp 10,000,000)
┌─────────────────────────────────────────────────────────┐
│ Dr. 103003000 (Finished Goods)      Rp 10,000,000      │
│ Cr. 103002000 (Work in Process)     Rp 10,000,000      │
└─────────────────────────────────────────────────────────┘
Result: Finished goods ready for sale

Step 5: Sales Order (SO-001, Rp 15,000,000)
┌─────────────────────────────────────────────────────────┐
│ Dr. 102000000 (Accounts Receivable) Rp 15,000,000      │
│ Cr. 401001000 (Sales Revenue)       Rp 15,000,000      │
└─────────────────────────────────────────────────────────┘
Result: Revenue recognized, receivable recorded

Step 6: Invoice Sent (INV-001, Subtotal Rp 15,000,000 + PPN 11%)
┌─────────────────────────────────────────────────────────┐
│ Dr. 102000000 (Accounts Receivable) Rp 16,650,000      │
│ Cr. 401001000 (Sales Revenue)       Rp 15,000,000      │
│ Cr. 202001000 (Output Tax)          Rp  1,650,000      │
└─────────────────────────────────────────────────────────┘
Result: Invoice with tax issued

Step 7: Payment Received (PAY-001, Rp 16,650,000)
┌─────────────────────────────────────────────────────────┐
│ Dr. 101000000 (Cash)                Rp 16,650,000      │
│ Cr. 102000000 (Accounts Receivable) Rp 15,000,000      │
│ Cr. 202001000 (Output Tax)          Rp  1,650,000      │
└─────────────────────────────────────────────────────────┘
Result: Cash received, receivable cleared, tax collected

═══════════════════════════════════════════════════════════
FINAL RESULT:
- Raw Material: Rp 0 (used in production)
- Work in Process: Rp 0 (completed to finished goods)
- Finished Goods: Rp 10,000,000 (available for next sale)
- Cash: +Rp 6,650,000 (received - paid)
- Revenue: +Rp 15,000,000
- Profit: Rp 5,000,000 (Revenue - Cost)
═══════════════════════════════════════════════════════════
```

---

## Error Handling Flow

```
┌─────────────────────────────────────────────────────────┐
│            TRANSACTION ERROR HANDLING                    │
└─────────────────────────────────────────────────────────┘

Controller Action
    │
    ├─► DB::beginTransaction()
    │
    ├─► Save main record (e.g., Purchase Order)
    │       │
    │       └─► SUCCESS ✓
    │
    ├─► AccountingService->recordXXX()
    │       │
    │       ├─► Get account IDs (cached)
    │       │
    │       ├─► Validate debit = credit
    │       │       │
    │       │       ├─► FAIL ✗ → Log error → return false
    │       │       └─► SUCCESS ✓
    │       │
    │       └─► Create journal entries
    │               │
    │               ├─► FAIL ✗ → Log error → return false
    │               └─► SUCCESS ✓
    │
    ├─► Check return value
    │       │
    │       ├─► FALSE → DB::rollback() → Show error
    │       └─► TRUE → DB::commit() → Success message
    │
    └─► END

If ANY step fails:
- All database changes are rolled back
- Error is logged to laravel.log
- User sees friendly error message
- Data integrity maintained
```

---

## Quick Reference

### Method Signatures

```php
// 1. Purchase Order Created
recordPurchaseOrderCreated(int $id, float $amount, string $date): bool

// 2. Goods Received
recordGoodsReceived(int $id, float $amount, string $date): bool

// 3. Production Process
recordProductionProcess(int $id, float $amount, string $date): bool

// 4. Sales Order
recordSalesOrder(int $id, float $amount, string $date): bool

// 5. Invoice Sent
recordInvoiceSent(int $id, float $subtotal, float $tax, string $date): bool

// 6. Customer Payment
recordCustomerPayment(int $id, float $total, float $tax, string $date): bool

// 7. Supplier Payment
recordSupplierPayment(int $id, float $amount, string $date): bool

// Cleanup
deleteJournalEntries(int $id, string $transactionName): bool
```

---

This diagram provides a complete visual overview of how the Accounting Service integrates with your business processes!
