<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'label_en','label_ar'
    ];
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
