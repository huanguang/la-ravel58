<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecsSelect extends Model
{
    use SoftDeletes;

    protected $table = 'product_specs_select';
}
