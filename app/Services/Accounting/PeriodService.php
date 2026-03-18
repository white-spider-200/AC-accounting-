<?php

namespace App\Services\Accounting;

use App\Models\GlAccountingPeriod;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PeriodService
{
    public function ensureDateIsOpen(string $date): ?GlAccountingPeriod
    {
        $period = $this->resolvePeriodForDate($date);

        if ($period && strtolower((string) $period->status) === 'closed') {
            throw ValidationException::withMessages([
                'entry_date' => __('Selected date falls in a closed accounting period.'),
            ]);
        }

        return $period;
    }

    public function resolvePeriodForDate(string $date): ?GlAccountingPeriod
    {
        $asDate = Carbon::parse($date)->toDateString();
        $periodCode = Carbon::parse($date)->format('Y-m');

        return GlAccountingPeriod::query()
            ->where(function ($query) use ($asDate, $periodCode) {
                $query->where(function ($subQuery) use ($asDate) {
                    $subQuery->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->whereDate('start_date', '<=', $asDate)
                        ->whereDate('end_date', '>=', $asDate);
                })->orWhere('period', $periodCode);
            })
            ->orderByDesc('start_date')
            ->first();
    }

    public function createOrUpdateFromCode(string $periodCode, string $status, ?string $notes = null, ?int $id = null): GlAccountingPeriod
    {
        $start = Carbon::createFromFormat('Y-m', $periodCode)->startOfMonth()->toDateString();
        $end = Carbon::createFromFormat('Y-m', $periodCode)->endOfMonth()->toDateString();

        $payload = [
            'period' => $periodCode,
            'name' => $periodCode,
            'start_date' => $start,
            'end_date' => $end,
            'status' => $status,
            'closed_at' => $status === 'closed' ? now() : null,
            'notes' => $notes,
        ];

        if ($id) {
            $period = GlAccountingPeriod::query()->findOrFail($id);
            $period->fill($payload)->save();

            return $period;
        }

        return GlAccountingPeriod::query()->create($payload);
    }
}
