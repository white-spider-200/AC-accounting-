<?php

namespace App\Services\Accounting;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayableReportService
{
    public function __construct(private readonly MappingService $mappingService)
    {
    }

    public function generate(array $filters): array
    {
        $paymentsByPurchase = DB::table('payments')
            ->select('purchase_id', DB::raw('SUM(amount) as paid_total'))
            ->groupBy('purchase_id');

        $rows = DB::table('purchases')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->leftJoinSub($paymentsByPurchase, 'payment_totals', 'payment_totals.purchase_id', '=', 'purchases.id')
            ->when(! empty($filters['warehouse_id']), fn ($query) => $query->where('purchases.warehouse_id', $filters['warehouse_id']))
            ->when(! empty($filters['supplier_id']), fn ($query) => $query->where('purchases.supplier_id', $filters['supplier_id']))
            ->when(! empty($filters['from']), fn ($query) => $query->whereDate('purchases.purchase_date', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn ($query) => $query->whereDate('purchases.purchase_date', '<=', $filters['to']))
            ->orderByDesc('purchases.purchase_date')
            ->orderByDesc('purchases.id')
            ->get([
                'purchases.id',
                'purchases.purchase_date',
                'purchases.total_amount',
                DB::raw("COALESCE(suppliers.name, 'Unknown Supplier') as supplier_name"),
                DB::raw('COALESCE(payment_totals.paid_total, 0) as paid_total'),
            ])
            ->map(function ($row) {
                $grandTotal = (float) $row->total_amount;
                $paid = (float) $row->paid_total;
                $due = max($grandTotal - $paid, 0);

                return [
                    'id' => $row->id,
                    'date' => $row->purchase_date,
                    'supplier_name' => $row->supplier_name,
                    'grand_total' => $grandTotal,
                    'paid' => $paid,
                    'due' => $due,
                    'status' => $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid'),
                    'status_class' => $due <= 0 ? 'success' : ($paid > 0 ? 'warning' : 'secondary'),
                ];
            });

        $apAccount = $this->mappingService->optionalCoreAccount(['accounts_payable']);
        $glBalance = 0.0;
        if ($apAccount) {
            $glBalance = (float) DB::table('gl_journal_entry_lines as lines')
                ->join('gl_journal_entries as entries', 'entries.id', '=', 'lines.journal_entry_id')
                ->where('entries.status', 'posted')
                ->where('lines.gl_account_id', $apAccount->id)
                ->when(! empty($filters['from']), fn ($q) => $q->whereDate('entries.entry_date', '>=', $filters['from']))
                ->when(! empty($filters['to']), fn ($q) => $q->whereDate('entries.entry_date', '<=', $filters['to']))
                ->selectRaw('COALESCE(SUM(lines.credit - lines.debit), 0) as balance')
                ->value('balance');
        }

        return [
            'rows' => $rows,
            'aging' => $this->legacyAging($this->agingBuckets($rows, 'due', 'date')),
            'total_payables' => (float) $rows->sum('due'),
            'gl_control_balance' => $glBalance,
        ];
    }

    private function agingBuckets(Collection $rows, string $amountKey, string $dateKey): array
    {
        $aging = [
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
        ];

        $today = now()->startOfDay();

        foreach ($rows as $row) {
            if (($row[$amountKey] ?? 0) <= 0 || empty($row[$dateKey])) {
                continue;
            }

            $days = Carbon::parse($row[$dateKey])->diffInDays($today);

            if ($days <= 30) {
                $aging['0_30'] += $row[$amountKey];
                continue;
            }

            if ($days <= 60) {
                $aging['31_60'] += $row[$amountKey];
                continue;
            }

            if ($days <= 90) {
                $aging['61_90'] += $row[$amountKey];
                continue;
            }

            $aging['90_plus'] += $row[$amountKey];
        }

        return $aging;
    }

    private function legacyAging(array $aging): array
    {
        return [
            '0_30' => $aging['0_30'],
            '31_60' => $aging['31_60'],
            '60_plus' => $aging['61_90'] + $aging['90_plus'],
            '61_90' => $aging['61_90'],
            '90_plus' => $aging['90_plus'],
        ];
    }
}
