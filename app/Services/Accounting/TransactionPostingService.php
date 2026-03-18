<?php

namespace App\Services\Accounting;

use App\Models\Expense;
use App\Models\GlJournalEntry;
use App\Models\Payment;
use App\Models\PaymentSale;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class TransactionPostingService
{
    public function __construct(
        private readonly JournalPostingService $journalPostingService,
        private readonly MappingService $mappingService,
    ) {
    }

    public function postSale(Sale $sale): GlJournalEntry
    {
        return $this->replaceSourceEntry('sale', $sale->id, function () use ($sale) {
            $arAccount = $this->mappingService->coreAccount('accounts_receivable');
            $salesRevenueAccount = $this->mappingService->coreAccount('sales_revenue');
            $cogsAccount = $this->mappingService->coreAccount('cogs');
            $inventoryAccount = $this->mappingService->coreAccount('inventory');
            $vatOutputAccount = $this->mappingService->optionalCoreAccount(['output_vat', 'vat_output']);

            $gross = (float) $sale->total_amount;
            $vat = (float) ($sale->tax_amount ?? 0);
            $netSales = max($gross - $vat, 0);
            $cogs = $this->saleCogs($sale);

            $lines = [
                [
                    'gl_account_id' => $arAccount->id,
                    'description' => 'Sale receivable',
                    'debit' => $gross,
                    'credit' => 0,
                    'client_id' => $sale->client_id,
                    'invoice_id' => $sale->id,
                ],
                [
                    'gl_account_id' => $salesRevenueAccount->id,
                    'description' => 'Sales revenue',
                    'debit' => 0,
                    'credit' => $netSales,
                    'client_id' => $sale->client_id,
                    'invoice_id' => $sale->id,
                ],
            ];

            if ($vat > 0 && $vatOutputAccount) {
                $lines[] = [
                    'gl_account_id' => $vatOutputAccount->id,
                    'description' => 'Output VAT',
                    'debit' => 0,
                    'credit' => $vat,
                    'client_id' => $sale->client_id,
                    'invoice_id' => $sale->id,
                ];
            }

            if ($cogs > 0) {
                $lines[] = [
                    'gl_account_id' => $cogsAccount->id,
                    'description' => 'Cost of goods sold',
                    'debit' => $cogs,
                    'credit' => 0,
                    'client_id' => $sale->client_id,
                    'invoice_id' => $sale->id,
                ];

                $lines[] = [
                    'gl_account_id' => $inventoryAccount->id,
                    'description' => 'Inventory reduction',
                    'debit' => 0,
                    'credit' => $cogs,
                    'client_id' => $sale->client_id,
                    'invoice_id' => $sale->id,
                ];
            }

            return $this->journalPostingService->post([
                'entry_date' => (string) ($sale->sale_date ?: now()->toDateString()),
                'description' => "Sale #{$sale->id}",
                'status' => 'posted',
                'reference_no' => $sale->invoice_number ?: ('SAL-' . $sale->id),
                'source_type' => 'sale',
                'source_id' => $sale->id,
                'created_by' => auth()->id(),
                'lines' => $lines,
            ]);
        });
    }

    public function postCustomerPayment(PaymentSale $paymentSale): GlJournalEntry
    {
        $payment = $paymentSale->payment;

        return $this->replaceSourceEntry('sale_payment', $paymentSale->id, function () use ($paymentSale, $payment) {
            $cashAccount = $this->mappingService->paymentTypeAccount($payment?->payment_type_id);
            $arAccount = $this->mappingService->coreAccount('accounts_receivable');
            $amount = (float) $paymentSale->amount;

            return $this->journalPostingService->post([
                'entry_date' => (string) ($payment?->payment_date ?: now()->toDateString()),
                'description' => "Customer payment for sale #{$paymentSale->sale_id}",
                'status' => 'posted',
                'reference_no' => $payment?->reference_number ?: ('SPM-' . $paymentSale->id),
                'source_type' => 'sale_payment',
                'source_id' => $paymentSale->id,
                'created_by' => $payment?->user_id ?: auth()->id(),
                'lines' => [
                    [
                        'gl_account_id' => $cashAccount->id,
                        'description' => 'Cash received',
                        'debit' => $amount,
                        'credit' => 0,
                        'client_id' => $paymentSale->sale?->client_id,
                        'invoice_id' => $paymentSale->sale_id,
                    ],
                    [
                        'gl_account_id' => $arAccount->id,
                        'description' => 'Receivable settled',
                        'debit' => 0,
                        'credit' => $amount,
                        'client_id' => $paymentSale->sale?->client_id,
                        'invoice_id' => $paymentSale->sale_id,
                    ],
                ],
            ]);
        });
    }

    public function postPurchase(Purchase $purchase): GlJournalEntry
    {
        return $this->replaceSourceEntry('purchase', $purchase->id, function () use ($purchase) {
            $inventoryAccount = $this->mappingService->optionalCoreAccount(['purchase_inventory', 'inventory'])
                ?? $this->mappingService->coreAccount('inventory');
            $apAccount = $this->mappingService->coreAccount('accounts_payable');
            $vatInputAccount = $this->mappingService->optionalCoreAccount(['input_vat', 'vat_input']);

            $gross = (float) $purchase->total_amount;
            $vat = (float) ($purchase->tax_amount ?? 0);
            $net = max($gross - $vat, 0);

            $lines = [
                [
                    'gl_account_id' => $inventoryAccount->id,
                    'description' => 'Purchase',
                    'debit' => $net,
                    'credit' => 0,
                    'supplier_id' => $purchase->supplier_id,
                    'invoice_id' => $purchase->id,
                ],
            ];

            if ($vat > 0 && $vatInputAccount) {
                $lines[] = [
                    'gl_account_id' => $vatInputAccount->id,
                    'description' => 'Input VAT',
                    'debit' => $vat,
                    'credit' => 0,
                    'supplier_id' => $purchase->supplier_id,
                    'invoice_id' => $purchase->id,
                ];
            }

            $lines[] = [
                'gl_account_id' => $apAccount->id,
                'description' => 'Accounts payable',
                'debit' => 0,
                'credit' => $gross,
                'supplier_id' => $purchase->supplier_id,
                'invoice_id' => $purchase->id,
            ];

            return $this->journalPostingService->post([
                'entry_date' => (string) ($purchase->purchase_date ?: now()->toDateString()),
                'description' => "Purchase #{$purchase->id}",
                'status' => 'posted',
                'reference_no' => $purchase->invoice_number ?: ('PUR-' . $purchase->id),
                'source_type' => 'purchase',
                'source_id' => $purchase->id,
                'created_by' => auth()->id(),
                'lines' => $lines,
            ]);
        });
    }

    public function postSupplierPayment(Payment $payment): GlJournalEntry
    {
        if (! $payment->purchase_id) {
            throw new \RuntimeException('Supplier payment posting requires purchase_id.');
        }

        return $this->replaceSourceEntry('purchase_payment', $payment->id, function () use ($payment) {
            $cashAccount = $this->mappingService->paymentTypeAccount($payment->payment_type_id);
            $apAccount = $this->mappingService->coreAccount('accounts_payable');
            $amount = (float) $payment->amount;

            return $this->journalPostingService->post([
                'entry_date' => (string) ($payment->payment_date ?: now()->toDateString()),
                'description' => "Supplier payment for purchase #{$payment->purchase_id}",
                'status' => 'posted',
                'reference_no' => $payment->reference_number ?: ('PPM-' . $payment->id),
                'source_type' => 'purchase_payment',
                'source_id' => $payment->id,
                'created_by' => $payment->user_id ?: auth()->id(),
                'lines' => [
                    [
                        'gl_account_id' => $apAccount->id,
                        'description' => 'Payable settled',
                        'debit' => $amount,
                        'credit' => 0,
                        'supplier_id' => $payment->purchase?->supplier_id,
                        'invoice_id' => $payment->purchase_id,
                    ],
                    [
                        'gl_account_id' => $cashAccount->id,
                        'description' => 'Cash paid',
                        'debit' => 0,
                        'credit' => $amount,
                        'supplier_id' => $payment->purchase?->supplier_id,
                        'invoice_id' => $payment->purchase_id,
                    ],
                ],
            ]);
        });
    }

    public function postExpense(Expense $expense): GlJournalEntry
    {
        return $this->replaceSourceEntry('expense', $expense->id, function () use ($expense) {
            $expenseAccount = $this->mappingService->expenseCategoryAccount($expense->expenses_category_id);
            $cashAccount = $this->mappingService->paymentTypeAccount(null);

            $amount = (float) $expense->amount;

            return $this->journalPostingService->post([
                'entry_date' => (string) ($expense->expense_date ?: now()->toDateString()),
                'description' => "Expense #{$expense->id}",
                'status' => 'posted',
                'reference_no' => 'EXP-' . $expense->id,
                'source_type' => 'expense',
                'source_id' => $expense->id,
                'created_by' => auth()->id(),
                'lines' => [
                    [
                        'gl_account_id' => $expenseAccount->id,
                        'description' => 'Expense recognition',
                        'debit' => $amount,
                        'credit' => 0,
                        'client_id' => $expense->client_id,
                        'invoice_id' => $expense->id,
                    ],
                    [
                        'gl_account_id' => $cashAccount->id,
                        'description' => 'Cash payment',
                        'debit' => 0,
                        'credit' => $amount,
                        'client_id' => $expense->client_id,
                        'invoice_id' => $expense->id,
                    ],
                ],
            ]);
        });
    }

    public function postOpeningBalance(int $accountId, float $amount, string $entryDate, string $description): GlJournalEntry
    {
        $equityAccount = $this->mappingService->optionalCoreAccount(['opening_balance_equity'])
            ?? $this->mappingService->optionalCoreAccount(['retained_earnings'])
            ?? \App\Models\GlAccount::query()->where('code', '3100')->firstOrFail();

        return $this->journalPostingService->post([
            'entry_date' => $entryDate,
            'description' => $description,
            'status' => 'posted',
            'reference_no' => 'OB-' . now()->format('YmdHis'),
            'source_type' => 'opening_balance',
            'source_id' => $accountId,
            'created_by' => auth()->id(),
            'is_opening' => true,
            'lines' => [
                [
                    'gl_account_id' => $accountId,
                    'description' => 'Opening balance',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'gl_account_id' => $equityAccount->id,
                    'description' => 'Opening balance equity',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function replaceSourceEntry(string $sourceType, int $sourceId, callable $poster): GlJournalEntry
    {
        return DB::transaction(function () use ($sourceType, $sourceId, $poster) {
            $existing = GlJournalEntry::query()
                ->with('lines')
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->whereIn('status', ['posted', 'draft'])
                ->get();

            foreach ($existing as $entry) {
                if ($entry->status === 'posted') {
                    $this->journalPostingService->reverseEntry($entry, now()->toDateString(), "Auto reversal for {$sourceType} #{$sourceId}");
                }

                if ($entry->status === 'draft') {
                    $entry->delete();
                }
            }

            return $poster();
        });
    }

    protected function saleCogs(Sale $sale): float
    {
        return (float) DB::table('sale_details')
            ->leftJoin('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sale_details.sale_id', $sale->id)
            ->selectRaw('SUM(sale_details.quantity * COALESCE(products.cost_price, sale_details.unit_price)) as total')
            ->value('total');
    }
}
