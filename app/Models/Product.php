<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency_id','comment','label_en','label_ar','cost_price','product_category_id','img','tax','code','stock_alert','measure_id','product_brand_id','price','details_ar','details_en','parent_id'
    ];

    public function category()
    {
        return $this->belongsTo(ProductsCategory::class,'product_category_id');
    }
    public function measure()
    {
        return $this->belongsTo(Measure::class,'measure_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function variants()
    {
        return $this->belongsToMany(Variant::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function combinations()
    {
        return $this->hasMany(ProductCombination::class);
    }
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class);
    }
    public static function getProductsByWarehouse($defaultWarehouseId, $perPage = 10, $page = 1,$category=0)
    {
        // The legacy query relaxes MySQL SQL mode to allow grouping by product id
        // while selecting product columns. SQLite does not support this statement.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))');
        }

        $operand = ($category > 0) ? '=' :'>' ;
        $query = Product::leftJoin('product_warehouse', 'product_warehouse.product_id', '=', 'products.id')
            ->select('products.*', DB::raw('SUM(product_warehouse.qty) as quantity'))
            ->where('product_warehouse.warehouse_id', $defaultWarehouseId)
            ->where('products.product_category_id',$operand,$category)
            ->groupBy('products.id')
            ->having('quantity', '>', 0);

        $total = $query->count();

        $offset = ($page - 1) * $perPage;

        $products = $query->offset($offset)
            ->limit($perPage)
            ->get();

        $suggestions = [];
        foreach ($products as $product) {
            $suggestions[] = [
                'name' => $product->label_en,
                'cost_price' => $product->cost_price,
                'tax' => $product->tax,
                'id' => $product->id,
                'code' => $product->code,
                'qty' => $product->quantity,
                'measure_id' => $product->measure_id,
                'measure' => optional($product->measure)->label_en,
                'currency' => optional($product->currency)->code_en,
                'image' => '/uploads/images/products/' . (empty($product->img) ? 'default.png' : $product->img),
            ];
        }

        return [
            'data' => $suggestions,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

}
