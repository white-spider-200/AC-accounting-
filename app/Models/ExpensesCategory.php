<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensesCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment','label_en','label_ar'
    ];
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
