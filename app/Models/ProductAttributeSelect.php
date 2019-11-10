<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeSelect extends Model
{
    use SoftDeletes;

    protected $table = 'product_attribute_select';
}
