<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'is_active',
        'sort_order',
    ];
}

