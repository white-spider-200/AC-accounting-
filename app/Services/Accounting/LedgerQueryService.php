<?php

namespace App\Services\Accounting;

use App\Models\GlJournalEntryLine;
use Illuminate\Database\Eloquent\Builder;

class LedgerQueryService
{
    public function linesQuery(?string $from = null, ?string $to = null): Builder
    {
        return GlJournalEntryLine::query()
            ->with(['account', 'entry'])
            ->whereHas('entry', function ($query) use ($from, $to) {
                $query->where('status', 'posted')
                    ->when($from, fn ($q) => $q->whereDate('entry_date', '>=', $from))
                    ->when($to, fn ($q) => $q->whereDate('entry_date', '<=', $to));
            });
    }

    public function openingLinesQuery(string $beforeDate): Builder
    {
        return GlJournalEntryLine::query()
            ->with(['account', 'entry'])
            ->whereHas('entry', fn ($query) => $query
                ->where('status', 'posted')
                ->whereDate('entry_date', '<', $beforeDate));
    }
}
