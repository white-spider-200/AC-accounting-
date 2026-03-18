<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlAccount extends Model
{
    use HasFactory;

    protected $table = 'gl_accounts';

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'type',
        'category',
        'normal_balance',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(GlJournalEntryLine::class, 'gl_account_id');
    }

    public function isDebitNormal(): bool
    {
        return strtolower((string) $this->normal_balance) === 'debit';
    }
}
