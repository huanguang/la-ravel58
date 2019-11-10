<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSkuSpecsValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku_specs_value';
    protected $fillable = ['id','product_id','specs_select_id','product_specs','group_id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
