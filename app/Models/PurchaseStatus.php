<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'label_en','label_ar'
    ];
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
