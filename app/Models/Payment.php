<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_status_id',
        'payment_type_id',
        'amount',
        'payment_date',
        'reference_number',
        'notes',
        'purchase_id',
        'user_id',
        'paid',
        'real_date',
        'comment',
    ];

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function setPaidAttribute($value)
    {
        $this->attributes['amount'] = $value;
    }
    public function getPaidAttribute()
    {
        return (float) ($this->attributes['amount'] ?? 0);
    }
    public function setRealDateAttribute($value)
    {
        $this->attributes['payment_date'] = $value;
    }
    public function getRealDateAttribute()
    {
        return $this->attributes['payment_date'] ?? null;
    }
    public function setCommentAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }
    public function getCommentAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

}
