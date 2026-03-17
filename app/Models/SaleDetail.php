<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'tax', 'qty', 'price', 'total'
    ];
public $timestamps = true;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function sale()
    {
        return $this-> belongsTo(Sale::class);
    }
    public function product()
    {
        return $this-> belongsTo(Product::class);
    }
    public function getProductStockQuantity($wareHouseId,$productId)
    {
        // Retrieve the related product and warehouse
        $product = $this->product;
        $warehouse = $this->warehouse;
        $warehouse = Warehouse::find($wareHouseId);
        $qty = optional(optional($warehouse->products()->where('product_id', $productId)->withPivot('qty')->first())->pivot)->qty ?? 0;
        return $qty;
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
