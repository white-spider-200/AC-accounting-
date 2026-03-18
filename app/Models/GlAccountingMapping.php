<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlAccountingMapping extends Model
{
    use HasFactory;

    protected $table = 'gl_accounting_mappings';

    protected $fillable = [
        'group_type',
        'mapping_key',
        'key',
        'name',
        'label',
        'reference_id',
        'gl_account_id',
        'debit_account_id',
        'credit_account_id',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'bool',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'gl_account_id');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'credit_account_id');
    }
}
