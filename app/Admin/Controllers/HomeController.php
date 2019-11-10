<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeSelect;
use App\Models\ProductSpecs;
use App\Models\ProductSpecsSelect;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }

    public function product_attribute(){
        $q = request()->get('q');
        $options = ProductAttribute::where('category_id',$q)->select('id','name')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[] = ['text'=>$option->name,'id'=>$option->id];
        }
        return $selectOption;
    }

    public function product_attribute_select(){
        $q = request()->get('q');
        $options = ProductAttributeSelect::where('attribute_id',$q)->select('id','value')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[] = ['text'=>$option->value,'id'=>$option->id];
        }
        return $selectOption;
    }

    public function product_specs(){
        $q = request()->get('q');
        $options = ProductSpecs::where('group_id',$q)->select('id','name')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[] = ['text'=>$option->name,'id'=>$option->id];
        }
        return $selectOption;
    }

    public function product_specs_select(){
        $q = request()->get('q');
        $options = ProductSpecsSelect::where('specs_id',$q)->select('id','value')->get();
        $selectOption = [];
        foreach ($options as $option){

            $selectOption[] = ['text'=>$option->value,'id'=>$option->id];
        }
        return $selectOption;
    }
}
