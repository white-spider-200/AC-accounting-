<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'adjustment_date',
        'notes',
        'total_products',
        'real_date',
        'comment',
    ];

    public function setRealDateAttribute($value)
    {
        $this->attributes['adjustment_date'] = $value;
    }

    public function getRealDateAttribute()
    {
        return $this->attributes['adjustment_date'] ?? null;
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }

    public function getCommentAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function details()
    {
        return $this-> hasMany(AdjustmentDetail::class);
    }


}
