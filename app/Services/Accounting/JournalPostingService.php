<?php

namespace App\Services\Accounting;

use App\Models\GlJournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalPostingService
{
    public function __construct(private readonly PeriodService $periodService)
    {
    }

    public function post(array $payload): GlJournalEntry
    {
        $entryDate = Carbon::parse($payload['entry_date'])->toDateString();
        $status = $payload['status'] ?? 'posted';

        $lines = collect($payload['lines'] ?? [])->map(function (array $line): array {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            return [
                'gl_account_id' => (int) ($line['gl_account_id'] ?? 0),
                'description' => $line['description'] ?? null,
                'line_description' => $line['line_description'] ?? ($line['description'] ?? null),
                'debit' => $debit,
                'credit' => $credit,
                'client_id' => $line['client_id'] ?? null,
                'supplier_id' => $line['supplier_id'] ?? null,
                'invoice_id' => $line['invoice_id'] ?? null,
            ];
        })->filter(fn (array $line) => $line['gl_account_id'] > 0)->values();

        $this->validateLines($lines);

        $debitTotal = (float) $lines->sum('debit');
        $creditTotal = (float) $lines->sum('credit');

        if (abs($debitTotal - $creditTotal) > 0.0001) {
            throw ValidationException::withMessages([
                'lines' => 'Debits and credits must balance.',
            ]);
        }

        $period = $this->periodService->resolvePeriodForDate($entryDate);
        if ($status === 'posted') {
            $period = $this->periodService->ensureDateIsOpen($entryDate);
        }

        return DB::transaction(function () use ($payload, $entryDate, $status, $period, $debitTotal, $lines) {
            $entry = GlJournalEntry::query()->create([
                'entry_no' => $this->nextJournalEntryNumber(),
                'reference_no' => $payload['reference_no'] ?? null,
                'entry_date' => $entryDate,
                'description' => $payload['description'],
                'status' => $status,
                'amount' => $debitTotal,
                'source_type' => $payload['source_type'] ?? null,
                'source_id' => $payload['source_id'] ?? null,
                'period_id' => $payload['period_id'] ?? $period?->id,
                'created_by' => $payload['created_by'] ?? auth()->id(),
                'reversed_entry_id' => $payload['reversed_entry_id'] ?? null,
                'is_opening' => (bool) ($payload['is_opening'] ?? false),
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create($line);
            }

            return $entry->fresh('lines');
        });
    }

    public function reverseEntry(GlJournalEntry $entry, ?string $date = null, ?string $description = null): GlJournalEntry
    {
        if (strtolower((string) $entry->status) !== 'posted') {
            throw ValidationException::withMessages([
                'entry' => 'Only posted journal entries can be reversed.',
            ]);
        }

        $reverseDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $this->periodService->ensureDateIsOpen($reverseDate);

        $reversalLines = $entry->lines->map(fn ($line) => [
            'gl_account_id' => $line->gl_account_id,
            'description' => $line->line_description ?: $line->description,
            'debit' => (float) $line->credit,
            'credit' => (float) $line->debit,
            'client_id' => $line->client_id,
            'supplier_id' => $line->supplier_id,
            'invoice_id' => $line->invoice_id,
        ])->all();

        $reversalEntry = $this->post([
            'entry_date' => $reverseDate,
            'description' => $description ?: "Reversal of {$entry->entry_no}",
            'status' => 'posted',
            'reference_no' => $entry->reference_no,
            'source_type' => 'reversal',
            'source_id' => $entry->id,
            'reversed_entry_id' => $entry->id,
            'created_by' => auth()->id(),
            'lines' => $reversalLines,
        ]);

        $entry->update([
            'status' => 'reversed',
            'reversed_entry_id' => $reversalEntry->id,
        ]);

        return $reversalEntry;
    }

    protected function validateLines(Collection $lines): void
    {
        if ($lines->count() < 2) {
            throw ValidationException::withMessages([
                'lines' => 'At least two journal lines are required.',
            ]);
        }

        foreach ($lines as $index => $line) {
            if ($line['debit'] < 0 || $line['credit'] < 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => 'Debit/Credit cannot be negative.',
                ]);
            }

            if ($line['debit'] > 0 && $line['credit'] > 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => 'A line cannot contain both debit and credit values.',
                ]);
            }

            if ($line['debit'] <= 0 && $line['credit'] <= 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => 'Each line must have either a debit or a credit amount.',
                ]);
            }
        }
    }

    protected function nextJournalEntryNumber(): string
    {
        $nextId = (int) GlJournalEntry::query()->max('id') + 1;

        return 'JE-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 8, '0', STR_PAD_LEFT);
    }
}
