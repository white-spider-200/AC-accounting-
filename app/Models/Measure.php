<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'comment','code_en','code_ar','label_en','label_ar'
    ];
}
