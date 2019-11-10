<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSkus extends Model
{
    use SoftDeletes;

    protected $table = 'product_skus';

    protected $fillable = ['id','product_id','code','name','price','image','stock','sales_volume','category_id','product_attribute','product_attribute_select'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
