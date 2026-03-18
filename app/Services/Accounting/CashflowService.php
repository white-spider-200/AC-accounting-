<?php

namespace App\Services\Accounting;

use App\Models\GlJournalEntryLine;

class CashflowService
{
    public function __construct(private readonly MappingService $mappingService)
    {
    }

    public function generate(?string $from, ?string $to): array
    {
        $cashAccounts = array_filter([
            $this->mappingService->optionalCoreAccount(['cash_account', 'sale_cash'])?->id,
            $this->mappingService->optionalCoreAccount(['bank_account'])?->id,
            1110,
            1120,
        ]);

        $lines = GlJournalEntryLine::query()
            ->with(['entry'])
            ->whereIn('gl_account_id', $cashAccounts)
            ->whereHas('entry', function ($query) use ($from, $to) {
                $query->where('status', 'posted')
                    ->when($from, fn ($q) => $q->whereDate('entry_date', '>=', $from))
                    ->when($to, fn ($q) => $q->whereDate('entry_date', '<=', $to));
            })
            ->get();

        $bucketed = [
            'operating' => 0.0,
            'investing' => 0.0,
            'financing' => 0.0,
        ];

        foreach ($lines as $line) {
            $movement = (float) $line->debit - (float) $line->credit;
            $sourceType = strtolower((string) ($line->entry?->source_type ?? 'manual'));

            if (in_array($sourceType, ['sale', 'sale_payment', 'purchase', 'purchase_payment', 'expense'], true)) {
                $bucketed['operating'] += $movement;
                continue;
            }

            if (in_array($sourceType, ['asset_purchase', 'asset_sale'], true)) {
                $bucketed['investing'] += $movement;
                continue;
            }

            $bucketed['financing'] += $movement;
        }

        return [
            'operating' => $bucketed['operating'],
            'investing' => $bucketed['investing'],
            'financing' => $bucketed['financing'],
            'incoming_total' => max($bucketed['operating'], 0) + max($bucketed['investing'], 0) + max($bucketed['financing'], 0),
            'outgoing_total' => abs(min($bucketed['operating'], 0)) + abs(min($bucketed['investing'], 0)) + abs(min($bucketed['financing'], 0)),
            'net_cash' => $bucketed['operating'] + $bucketed['investing'] + $bucketed['financing'],
        ];
    }
}
