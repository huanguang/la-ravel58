<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/product_attribute', 'HomeController@product_attribute')->name('admin.home');//获取
    $router->get('/product_attribute_select', 'HomeController@product_attribute_select')->name('admin.home');//获取
    $router->get('/product_specs', 'HomeController@product_specs')->name('admin.home');//获取
    $router->get('/product_specs_select', 'HomeController@product_specs_select')->name('admin.home');//获取
    $router->resource('product', ProductController::class); //产品列表
    $router->resource('ProductAttribute', ProductAttributeController::class); //属性列表
    $router->resource('ProductAttributeSelect', ProductAttributeSelectController::class); //属性选项
    $router->resource('ProductBrand', ProductBrandController::class); //品牌
    $router->resource('ProductCategory', ProductCategoryController::class); //产品分类
    $router->resource('ProductSku', ProductSkuController::class); //产品sku
    $router->resource('ProductSkuSpecsValue', ProductSkuSpecsValueController::class); //sku-规格关联
    $router->resource('ProductSpecs', ProductSpecsController::class); //产品规格
    $router->resource('ProductSpecsGroup', ProductSpecsGroupController::class); //产品规格组
    $router->resource('ProductSpecsSelect', ProductSpecsSelectController::class); //产品列表
});
