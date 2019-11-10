<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\ProductSkuSpecsValue;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecs;
use App\Models\ProductSpecsGroup;
use App\Models\ProductSpecsSelect;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductSkuSpecsValueController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductSkuSpecsValue);

        $grid->id('ID')->sortable();
        $grid->product_id('产品')->display(function ($product_id) {
            return Product::find($product_id)->name ?? '';
        });
        $grid->group_id('规格组')->display(function ($group_id) {
            return ProductSpecsGroup::find($group_id)->name ?? '';
        });
        $grid->product_specs('规格')->display(function ($product_specs) {
            return ProductSpecs::find($product_specs)->name ?? '';
        });
        $grid->specs_select_id('规格值')->display(function ($specs_select_id) {
            return ProductSpecsSelect::find($specs_select_id)->value ?? '';
        });
        $grid->created_at('Created at')->sortable();
        $grid->updated_at('Updated at')->sortable();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableRowSelector();
        $grid->disableFilter();
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '名称');
            $filter->like('code', '编号');

        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ProductSkuSpecsValue::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProductSkuSpecsValue);

        $form->display('ID');
        $form->display('Created at');
        $form->display('Updated at');

        return $form;
    }
}
