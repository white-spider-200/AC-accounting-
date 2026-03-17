<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','comment','label_en','label_ar','parent_id','img'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function children()
    {
        return $this->hasMany(ProductsCategory::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductsCategory::class, 'parent_id');
    }
}
