<?php

namespace App\Admin\Controllers;

use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductSpecs;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecsGroup;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductSpecsController extends Controller
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
        $grid = new Grid(new ProductSpecs);
        $grid->id('ID')->sortable();
        $grid->name('名称')->sortable();
        $grid->category_id('分类')->display(function ($category_id) {
            return ProductCategory::find($category_id)->name ?? '';
        });
        $grid->group_id('属性组')->display(function ($group_id) {
            return ProductSpecsGroup::find($group_id)->name ?? '';
        });
        $grid->created_at('Created at')->sortable();
        $grid->updated_at('Updated at')->sortable();
        $grid->actions(function ($actions) {
            $actions->disableView();

        });
        $grid->disableExport();
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
        $show = new Show(ProductSpecs::findOrFail($id));

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
        $form = new Form(new ProductSpecs);
        $form->text('name','名称');
        $form->select('category_id', '分类')->options(ProductCategory::getSelectOptions())->default('1');
        $form->select('group_id', '属性组')->options(ProductAttribute::getSelectOptions())->default('1');
        return $form;
    }
}
