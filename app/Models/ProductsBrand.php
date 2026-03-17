<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsBrand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','comment','label_en','label_ar','parent_id','img'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
