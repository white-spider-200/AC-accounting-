<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentSale;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\Accounting\SetupService;
use App\Services\Accounting\TransactionPostingService;
use Illuminate\Console\Command;

class AccountingBackfillCommand extends Command
{
    protected $signature = 'accounting:backfill {--from=} {--to=} {--chunk=100}';

    protected $description = 'Backfill ledger journal postings from existing business transactions';

    public function __construct(
        private readonly SetupService $setupService,
        private readonly TransactionPostingService $transactionPostingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->setupService->ensureInitialized();

        $from = $this->option('from');
        $to = $this->option('to');
        $chunk = max((int) $this->option('chunk'), 1);

        $stats = [
            'sales' => ['ok' => 0, 'skip' => 0, 'fail' => 0],
            'sale_payments' => ['ok' => 0, 'skip' => 0, 'fail' => 0],
            'purchases' => ['ok' => 0, 'skip' => 0, 'fail' => 0],
            'purchase_payments' => ['ok' => 0, 'skip' => 0, 'fail' => 0],
            'expenses' => ['ok' => 0, 'skip' => 0, 'fail' => 0],
        ];

        Sale::query()
            ->when($from, fn ($q) => $q->whereDate('sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sale_date', '<=', $to))
            ->orderBy('id')
            ->chunkById($chunk, function ($sales) use (&$stats) {
                foreach ($sales as $sale) {
                    $this->attempt('sales', fn () => $this->transactionPostingService->postSale($sale), $stats, "sale:{$sale->id}");
                }
            });

        PaymentSale::query()
            ->with(['payment'])
            ->when($from, fn ($q) => $q->whereHas('payment', fn ($p) => $p->whereDate('payment_date', '>=', $from)))
            ->when($to, fn ($q) => $q->whereHas('payment', fn ($p) => $p->whereDate('payment_date', '<=', $to)))
            ->orderBy('id')
            ->chunkById($chunk, function ($rows) use (&$stats) {
                foreach ($rows as $row) {
                    $this->attempt('sale_payments', fn () => $this->transactionPostingService->postCustomerPayment($row), $stats, "sale_payment:{$row->id}");
                }
            });

        Purchase::query()
            ->when($from, fn ($q) => $q->whereDate('purchase_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('purchase_date', '<=', $to))
            ->orderBy('id')
            ->chunkById($chunk, function ($purchases) use (&$stats) {
                foreach ($purchases as $purchase) {
                    $this->attempt('purchases', fn () => $this->transactionPostingService->postPurchase($purchase), $stats, "purchase:{$purchase->id}");
                }
            });

        Payment::query()
            ->whereNotNull('purchase_id')
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->orderBy('id')
            ->chunkById($chunk, function ($rows) use (&$stats) {
                foreach ($rows as $row) {
                    $this->attempt('purchase_payments', fn () => $this->transactionPostingService->postSupplierPayment($row), $stats, "purchase_payment:{$row->id}");
                }
            });

        Expense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->orderBy('id')
            ->chunkById($chunk, function ($rows) use (&$stats) {
                foreach ($rows as $row) {
                    $this->attempt('expenses', fn () => $this->transactionPostingService->postExpense($row), $stats, "expense:{$row->id}");
                }
            });

        $this->table(['Stream', 'Posted', 'Skipped', 'Failed'], collect($stats)->map(fn ($line, $key) => [
            $key,
            $line['ok'],
            $line['skip'],
            $line['fail'],
        ]));

        return Command::SUCCESS;
    }

    private function attempt(string $stream, callable $action, array &$stats, string $tag): void
    {
        try {
            $action();
            $stats[$stream]['ok']++;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $stats[$stream]['skip']++;
            $this->warn("Skipped {$tag}: " . implode('; ', $e->errors()['mapping'] ?? $e->errors()['entry_date'] ?? ['validation failed']));
        } catch (\Throwable $e) {
            $stats[$stream]['fail']++;
            $this->error("Failed {$tag}: {$e->getMessage()}");
        }
    }
}
