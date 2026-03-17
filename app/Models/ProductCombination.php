<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductCombination extends Model
{
    protected $table = 'product_combinations';
    protected $fillable = [
        'original_combination','product_id','qty','code','generated_id','price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
