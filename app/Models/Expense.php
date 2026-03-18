<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'description',
        'expense_date',
        'expenses_category_id',
        'client_id',
        'comment',
        'real_date',
        'price',
        'expenses_categories_id',
    ];

    public function category()
    {
        return $this->belongsTo(ExpensesCategory::class, 'expenses_category_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function setRealDateAttribute($value)
    {
        $this->attributes['expense_date'] = $value;
    }

    public function getRealDateAttribute()
    {
        return $this->attributes['expense_date'] ?? null;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['amount'] = $value;
    }

    public function getPriceAttribute()
    {
        return (float) ($this->attributes['amount'] ?? 0);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['description'] = $value;
    }

    public function getCommentAttribute()
    {
        return $this->attributes['description'] ?? null;
    }

    public function setExpensesCategoriesIdAttribute($value)
    {
        $this->attributes['expenses_category_id'] = $value;
    }

    public function getExpensesCategoriesIdAttribute()
    {
        return $this->attributes['expenses_category_id'] ?? null;
    }

    public function getLabelEnAttribute()
    {
        return $this->attributes['label_en'] ?? $this->comment;
    }

    public function getLabelArAttribute()
    {
        return $this->attributes['label_ar'] ?? $this->comment;
    }
}
