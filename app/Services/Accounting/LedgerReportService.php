<?php

namespace App\Services\Accounting;

use App\Models\GlAccount;
use App\Models\GlJournalEntryLine;

class LedgerReportService
{
    public function accountLedger(int $accountId, ?string $from, ?string $to): array
    {
        $account = GlAccount::query()->findOrFail($accountId);

        $opening = (float) GlJournalEntryLine::query()
            ->join('gl_journal_entries as entries', 'entries.id', '=', 'gl_journal_entry_lines.journal_entry_id')
            ->where('entries.status', 'posted')
            ->where('gl_journal_entry_lines.gl_account_id', $accountId)
            ->when($from, fn ($q) => $q->whereDate('entries.entry_date', '<', $from))
            ->selectRaw('COALESCE(SUM(gl_journal_entry_lines.debit - gl_journal_entry_lines.credit), 0) as balance')
            ->value('balance');

        $running = $opening;
        $lines = GlJournalEntryLine::query()
            ->with('entry')
            ->where('gl_account_id', $accountId)
            ->whereHas('entry', function ($query) use ($from, $to) {
                $query->where('status', 'posted')
                    ->when($from, fn ($q) => $q->whereDate('entry_date', '>=', $from))
                    ->when($to, fn ($q) => $q->whereDate('entry_date', '<=', $to));
            })
            ->join('gl_journal_entries as entries', 'entries.id', '=', 'gl_journal_entry_lines.journal_entry_id')
            ->orderBy('entries.entry_date')
            ->orderBy('gl_journal_entry_lines.id')
            ->get(['gl_journal_entry_lines.*', 'entries.entry_date'])
            ->map(function ($line) use (&$running) {
                $running += (float) $line->debit - (float) $line->credit;

                return [
                    'date' => $line->entry_date,
                    'entry_no' => $line->entry?->entry_no,
                    'description' => $line->line_description ?: $line->description,
                    'debit' => (float) $line->debit,
                    'credit' => (float) $line->credit,
                    'running_balance' => $running,
                ];
            });

        return [
            'account' => $account,
            'opening_balance' => $opening,
            'lines' => $lines,
            'closing_balance' => $running,
        ];
    }
}
