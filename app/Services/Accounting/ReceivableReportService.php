<?php

namespace App\Services\Accounting;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReceivableReportService
{
    public function __construct(private readonly MappingService $mappingService)
    {
    }

    public function generate(array $filters): array
    {
        $rows = DB::table('sales')
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoinSub(
                DB::table('payment_sales')
                    ->select('sale_id', DB::raw('SUM(amount) as paid_total'))
                    ->groupBy('sale_id'),
                'payment_totals',
                'payment_totals.sale_id',
                '=',
                'sales.id'
            )
            ->when(! empty($filters['warehouse_id']), fn ($query) => $query->where('sales.warehouse_id', $filters['warehouse_id']))
            ->when(! empty($filters['client_id']), fn ($query) => $query->where('sales.client_id', $filters['client_id']))
            ->when(! empty($filters['from']), fn ($query) => $query->whereDate('sales.sale_date', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn ($query) => $query->whereDate('sales.sale_date', '<=', $filters['to']))
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id')
            ->get([
                'sales.id',
                'sales.sale_date',
                'sales.total_amount',
                DB::raw("COALESCE(clients.name, 'Walk In') as client_name"),
                DB::raw('COALESCE(payment_totals.paid_total, 0) as paid_total'),
            ])
            ->map(function ($row) {
                $grandTotal = (float) $row->total_amount;
                $paid = (float) $row->paid_total;
                $due = max($grandTotal - $paid, 0);

                return [
                    'id' => $row->id,
                    'date' => $row->sale_date,
                    'client_name' => $row->client_name,
                    'grand_total' => $grandTotal,
                    'paid' => $paid,
                    'due' => $due,
                    'status' => $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid'),
                    'status_class' => $due <= 0 ? 'success' : ($paid > 0 ? 'warning' : 'secondary'),
                ];
            });

        $arAccount = $this->mappingService->optionalCoreAccount(['accounts_receivable']);
        $glBalance = 0.0;
        if ($arAccount) {
            $glBalance = (float) DB::table('gl_journal_entry_lines as lines')
                ->join('gl_journal_entries as entries', 'entries.id', '=', 'lines.journal_entry_id')
                ->where('entries.status', 'posted')
                ->where('lines.gl_account_id', $arAccount->id)
                ->when(! empty($filters['from']), fn ($q) => $q->whereDate('entries.entry_date', '>=', $filters['from']))
                ->when(! empty($filters['to']), fn ($q) => $q->whereDate('entries.entry_date', '<=', $filters['to']))
                ->selectRaw('COALESCE(SUM(lines.debit - lines.credit), 0) as balance')
                ->value('balance');
        }

        return [
            'rows' => $rows,
            'aging' => $this->legacyAging($this->agingBuckets($rows, 'due', 'date')),
            'total_receivables' => (float) $rows->sum('due'),
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
