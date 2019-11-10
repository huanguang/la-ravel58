<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecsGroup extends Model
{
    use SoftDeletes;

    protected $table = 'product_specs_group';

    public static function getSelectOptions()
    {
        $options = ProductCategory::select('id','name')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->name;
        }
        return $selectOption;
    }

    public static function getSelectOptionss()
    {
        $options = ProductSpecsGroup::select('id','name')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->name;
        }
        return $selectOption;
    }
}
