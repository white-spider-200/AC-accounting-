<?php

namespace App\Services\Accounting;

use App\Models\GlJournalEntryLine;

class VatSummaryService
{
    public function __construct(private readonly MappingService $mappingService)
    {
    }

    public function generate(?string $from, ?string $to): array
    {
        $outputVatAccount = $this->mappingService->optionalCoreAccount(['output_vat', 'vat_output']);
        $inputVatAccount = $this->mappingService->optionalCoreAccount(['input_vat', 'vat_input']);

        $linesQuery = GlJournalEntryLine::query()
            ->with('entry')
            ->whereHas('entry', function ($query) use ($from, $to) {
                $query->where('status', 'posted')
                    ->when($from, fn ($q) => $q->whereDate('entry_date', '>=', $from))
                    ->when($to, fn ($q) => $q->whereDate('entry_date', '<=', $to));
            });

        $outputVat = 0.0;
        if ($outputVatAccount) {
            $outputVat = (float) (clone $linesQuery)
                ->where('gl_account_id', $outputVatAccount->id)
                ->sum('credit');
        }

        $inputVat = 0.0;
        if ($inputVatAccount) {
            $inputVat = (float) (clone $linesQuery)
                ->where('gl_account_id', $inputVatAccount->id)
                ->sum('debit');
        }

        $detailLines = (clone $linesQuery)
            ->when($outputVatAccount || $inputVatAccount, function ($query) use ($outputVatAccount, $inputVatAccount) {
                $ids = array_filter([$outputVatAccount?->id, $inputVatAccount?->id]);
                if (! empty($ids)) {
                    $query->whereIn('gl_account_id', $ids);
                }
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(fn (GlJournalEntryLine $line) => [
                'date' => optional($line->entry)->entry_date?->toDateString() ?? optional($line->entry)->entry_date,
                'entry_no' => $line->entry?->entry_no,
                'description' => $line->line_description ?: $line->description,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
            ]);

        return [
            'vat_collected' => $outputVat,
            'vat_paid' => $inputVat,
            'vat_net' => $outputVat - $inputVat,
            'summary' => [
                ['label' => 'VAT Collected (Sales)', 'value' => $outputVat],
                ['label' => 'VAT Paid (Purchases)', 'value' => $inputVat],
                ['label' => 'VAT Net', 'value' => $outputVat - $inputVat, 'highlight' => true],
            ],
            'lines' => $detailLines,
        ];
    }
}
