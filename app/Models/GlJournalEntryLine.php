<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlJournalEntryLine extends Model
{
    use HasFactory;

    protected $table = 'gl_journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'gl_account_id',
        'description',
        'line_description',
        'debit',
        'credit',
        'client_id',
        'supplier_id',
        'invoice_id',
    ];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(GlJournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'gl_account_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
