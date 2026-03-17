<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'label_en','label_ar','parent_id','code'
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    public function children()
    {
        return $this->hasMany(Variant::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Variant::class, 'parent_id');
    }
}
