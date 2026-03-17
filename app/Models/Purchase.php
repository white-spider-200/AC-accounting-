<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'purchase_status_id',
        'invoice_number',
        'purchase_date',
        'total_amount',
        'tax_amount',
        'notes',
        'discount',
        'order_tax',
        'shippment_price',
        'tax_whole_purchase_send',
        'payment_status_id',
        'comment',
        'status',
        'real_date',
        'grand_total',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function details()
    {
        return $this-> hasMany(PurchaseDetail::class);
    }
    public function statusName(){
        return $this->belongsTo(PurchaseStatus::class, 'purchase_status_id');
    }
    public function paymentStatus(){
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }

    public function getCommentAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['purchase_status_id'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['purchase_status_id'] ?? null;
    }

    public function setRealDateAttribute($value)
    {
        $this->attributes['purchase_date'] = $value;
    }

    public function getRealDateAttribute()
    {
        return $this->attributes['purchase_date'] ?? null;
    }

    public function setGrandTotalAttribute($value)
    {
        $this->attributes['total_amount'] = $value;
    }

    public function getGrandTotalAttribute()
    {
        return (float) ($this->attributes['total_amount'] ?? 0);
    }

    public function getPaidAttribute()
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }

        return (float) $this->payments()->sum('amount');
    }

    public function getDueAttribute()
    {
        return max($this->grand_total - $this->paid, 0);
    }

}
