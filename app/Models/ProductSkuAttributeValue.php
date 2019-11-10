<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSkuAttributeValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku_attribute_value';
}
