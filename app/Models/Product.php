<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';
    /*
    * 商品规格
    */
    public function product_attr()
    {
        return $this->hasMany(ProductSkus::class, 'product_id');
    }

    /*
    * 商品规格
    */
    public function product_sku_specs_value()
    {
        return $this->hasMany(ProductSkuSpecsValue::class, 'product_id');
    }

    /**
     * 设置商品图组
     * @param $pictures
     */
    public function setProductAlbumAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['product_album'] = json_encode($pictures);
        }
    }



    /**
     * 获取商品图组
     * @param $pictures
     * @return mixed
     */
    public function getProductAlbumAttribute($pictures)
    {
        return json_decode($pictures, true);
    }
}
