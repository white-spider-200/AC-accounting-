<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'type',
        'parent_id',
        'is_active',
    ];
}
