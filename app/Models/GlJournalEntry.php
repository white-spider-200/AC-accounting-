<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlJournalEntry extends Model
{
    use HasFactory;

    protected $table = 'gl_journal_entries';

    protected $fillable = [
        'entry_no',
        'reference_no',
        'entry_date',
        'description',
        'status',
        'amount',
        'source_type',
        'source_id',
        'period_id',
        'created_by',
        'reversed_entry_id',
        'is_opening',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'float',
        'is_opening' => 'bool',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(GlJournalEntryLine::class, 'journal_entry_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(GlAccountingPeriod::class, 'period_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversedEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversed_entry_id');
    }
}
