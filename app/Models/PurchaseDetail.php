<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'tax', 'qty', 'price', 'total'
    ];
public $timestamps = true;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchase()
    {
        return $this-> belongsTo(Purchase::class);
    }
    public function product()
    {
        return $this-> belongsTo(Product::class);
    }

    public function setQtyAttribute($value)
    {
        $this->attributes['quantity'] = $value;
    }

    public function getQtyAttribute()
    {
        return $this->attributes['quantity'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['unit_price'] ?? 0;
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total_price'] = $value;
    }

    public function getTotalAttribute()
    {
        return $this->attributes['total_price'] ?? 0;
    }
}
