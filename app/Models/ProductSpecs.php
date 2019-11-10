<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecs extends Model
{
    use SoftDeletes;

    protected $table = 'product_specs';

    public static function getSelectOptions()
    {
        $options = ProductSpecs::select('id','name')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->name;
        }
        return $selectOption;
    }
}
