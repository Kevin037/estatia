<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /**
     * Cache for account IDs by code
     */
    private static $accountCache = [];

    /**
     * Get account ID by code (with caching)
     */
    private function getAccountId(string $code): ?int
    {
        if (!isset(self::$accountCache[$code])) {
            $account = Account::where('code', $code)->first();
            self::$accountCache[$code] = $account ? $account->id : null;
        }
        return self::$accountCache[$code];
    }

    /**
     * Generate unique journal entry ID for a batch of entries
     */
    private function generateJournalEntryId(): int
    {
        $lastEntry = JournalEntry::orderBy('journal_entry_id', 'desc')->first();
        return $lastEntry ? $lastEntry->journal_entry_id + 1 : 1;
    }

    /**
     * Create journal entries
     * 
     * @param array $entries Array of entries with account_code, debit, credit
     * @param int $transactionId The ID of the related transaction
     * @param string $transactionName The name/type of transaction
     * @param string $date Transaction date
     * @param string|null $description Optional description
     * @return bool
     */
    private function createJournalEntries(
        array $entries,
        int $transactionId,
        string $transactionName,
        string $date,
        ?string $description = null
    ): bool {
        try {
            $journalEntryId = $this->generateJournalEntryId();
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $entry) {
                $accountId = $this->getAccountId($entry['account_code']);
                
                if (!$accountId) {
                    Log::error("Account not found: {$entry['account_code']}");
                    return false;
                }

                $debit = $entry['debit'] ?? 0;
                $credit = $entry['credit'] ?? 0;

                $totalDebit += $debit;
                $totalCredit += $credit;

                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'transaction_name' => $transactionName,
                    'dt' => $date,
                    'account_id' => $accountId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'desc' => $description,
                    'journal_entry_id' => $journalEntryId,
                ]);
            }

            // Verify double-entry bookkeeping (debit = credit)
            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                Log::error("Journal entry not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}");
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to create journal entries: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 1. Purchase Order Created (Raw Material Procurement)
     * Debit: 103001000 (Persediaan Barang Dagang)
     * Credit: 201002000 (Utang Bank - Supplier Payable)
     */
    public function recordPurchaseOrderCreated(int $purchaseOrderId, float $amount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '103001000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '201002000',
                'debit' => 0,
                'credit' => $amount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $purchaseOrderId,
            'PurchaseOrder',
            $date,
            'Purchase Order Created - Raw Material Procurement'
        );
    }

    /**
     * 2. Goods Received (When PO status changes to completed)
     * Debit: 103002000 (Persediaan Barang Bahan - Work in Process)
     * Credit: 103001000 (Persediaan Barang Dagang - Raw Material)
     */
    public function recordGoodsReceived(int $purchaseOrderId, float $amount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '103002000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '103001000',
                'debit' => 0,
                'credit' => $amount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $purchaseOrderId,
            'PurchaseOrder',
            $date,
            'Goods Received - Materials to Work in Process'
        );
    }

    /**
     * 3. Production Process
     * Debit: 103003000 (Persediaan Barang Jadi - Finished Goods)
     * Credit: 103002000 (Persediaan Barang Bahan - Work in Process)
     */
    public function recordProductionProcess(int $productionId, float $amount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '103003000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '103002000',
                'debit' => 0,
                'credit' => $amount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $productionId,
            'Production',
            $date,
            'Production Process - Finished Goods'
        );
    }

    /**
     * 4. Sales Order Created
     * Debit: 102000000 (Piutang Usaha - Accounts Receivable)
     * Credit: 401001000 (Pendapatan Jasa - Sales Revenue)
     */
    public function recordSalesOrder(int $orderId, float $amount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '102000000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '401001000',
                'debit' => 0,
                'credit' => $amount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $orderId,
            'Order',
            $date,
            'Sales Order Created'
        );
    }

    /**
     * 5. Invoice Sent
     * Debit: 102000000 (Piutang Usaha)
     * Credit: 401001000 (Pendapatan Jasa)
     * Debit: 202001000 (PPN Keluaran)
     * Credit: 202001000 (PPN Keluaran)
     * 
     * Note: The tax entry (debit & credit same account) effectively records the tax liability
     */
    public function recordInvoiceSent(int $invoiceId, float $amount, float $taxAmount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '102000000',
                'debit' => $amount + $taxAmount,
                'credit' => 0,
            ],
            [
                'account_code' => '401001000',
                'debit' => 0,
                'credit' => $amount,
            ],
            [
                'account_code' => '202001000',
                'debit' => 0,
                'credit' => $taxAmount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $invoiceId,
            'Invoice',
            $date,
            'Invoice Sent to Customer'
        );
    }

    /**
     * 6. Payment Received from Customer
     * Debit: 101000000 (Kas - Cash)
     * Credit: 102000000 (Piutang Usaha - Accounts Receivable)
     * Credit: 202001000 (PPN Keluaran - Output Tax)
     */
    public function recordCustomerPayment(int $paymentId, float $amount, float $taxAmount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '101000000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '102000000',
                'debit' => 0,
                'credit' => $amount - $taxAmount,
            ],
            [
                'account_code' => '202001000',
                'debit' => 0,
                'credit' => $taxAmount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $paymentId,
            'Payment',
            $date,
            'Payment Received from Customer'
        );
    }

    /**
     * 7. Payment to Supplier (When PO status changes to completed)
     * Debit: 201002000 (Utang Bank - Supplier Payable)
     * Credit: 101000000 (Kas - Cash)
     */
    public function recordSupplierPayment(int $purchaseOrderId, float $amount, string $date): bool
    {
        $entries = [
            [
                'account_code' => '201002000',
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_code' => '101000000',
                'debit' => 0,
                'credit' => $amount,
            ],
        ];

        return $this->createJournalEntries(
            $entries,
            $purchaseOrderId,
            'PurchaseOrder',
            $date,
            'Payment to Supplier'
        );
    }

    /**
     * Delete journal entries for a transaction
     */
    public function deleteJournalEntries(int $transactionId, string $transactionName): bool
    {
        try {
            JournalEntry::where('transaction_id', $transactionId)
                ->where('transaction_name', $transactionName)
                ->delete();
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete journal entries: " . $e->getMessage());
            return false;
        }
    }
}
