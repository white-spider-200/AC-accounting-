<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','warehouse_id','qty','adjustment_id','in_warehouse'
    ];
public $timestamps = true;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function adjustment()
    {
        return $this-> belongsTo(Adjustment::class);
    }
    public function product()
    {
        return $this-> belongsTo(Product::class);
    }
}
