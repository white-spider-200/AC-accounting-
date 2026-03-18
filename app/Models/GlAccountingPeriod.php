<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlAccountingPeriod extends Model
{
    use HasFactory;

    protected $table = 'gl_periods';

    protected $fillable = [
        'period',
        'name',
        'start_date',
        'end_date',
        'status',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(GlJournalEntry::class, 'period_id');
    }

    public function coversDate(Carbon $date): bool
    {
        if (! $this->start_date || ! $this->end_date) {
            return Carbon::parse($date)->format('Y-m') === $this->period;
        }

        return $date->betweenIncluded($this->start_date, $this->end_date);
    }
}
