<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ProductExporter;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeSelect;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSkuSpecsValue;
use App\Models\ProductSpecs;
use App\Models\ProductSpecsGroup;
use App\Models\ProductSpecsSelect;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use App\Models\ProductSku;

class ProductController extends Controller
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
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->name('名称')->expand(function ($model) {
            $comments = $model->product_attr()->get()->map(function ($comment) {
                return $comment->only(['image','name', 'code', 'price', 'stock', 'sales_volume', 'product_attribute','product_attribute_select','category_id']);
            });
            $list = $comments->toArray();
            if ($list) {
                foreach ($list as $key => $item) {
                    $list[$key]['product_attribute'] = ProductAttribute::find($item['product_attribute'])->name ?? '';
                    $list[$key]['product_attribute_select'] = ProductAttributeSelect::find($item['product_attribute_select'])->value ?? '';
                    $list[$key]['category_id'] = ProductCategory::find($item['category_id'])->name ?? '';
                    $list[$key]['image'] = "
    <img src=\"/{$item['image']}\" style=\"max-width:50px;max-height:50px\" class=\"img img-thumbnail\">
";
                }
            }
            return new Table(['图片','名称', '编号', '价格', '库存', '销量', '属性','属性值','分类'], $list);
        })->sortable();
        $grid->code('编号')->sortable();
        $grid->image('主图')->lightbox(['width' => 50, 'height' => 50]);
        $grid->category_id('商品分类')->display(function ($category_id) {
            return ProductCategory::find($category_id)->name ?? '';
        });
        $grid->brand_id('品牌')->display(function ($brand_id) {
            return ProductBrand::find($brand_id)->name ?? '';
        });
        $grid->stock('库存')->sortable();
        $grid->sales_volume('销量')->sortable();
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        //$grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('name', '名称');
            $filter->like('code', '编号');
        });
//        $excel = new ProductExporter();
//        $excel->setAttr(['id', '名称', '类型', '二维码', '上传人'], ['id', 'name', 'type', 'url', 'admin']);
//        $grid->exporter($excel);
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
        $show = new Show(Product::findOrFail($id));

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
        $form = new Form(new Product);

        $form->tab('基本信息', function ($form) {
            $form->text('name', '商品标题')->rules('required|max:100')->help('最大输入100字');
            //$form->text('subtitle','商品副标题')->required();
            $form->select('brand_id', '品牌')->options(ProductBrand::getSelectOptions())->default('1');
//            $form->text('product_sn', '商品编号')->rules(function ($form) {
//                // 如果不是编辑状态，则添加字段唯一验证
//                if (!$id = $form->model()->id) {
//                    return 'unique:product,product_sn';
//                }
//            })->help('为空时将自动生成商品编号');
            $form->text('code', '商品编号')->help('为空时将自动生成商品编号');
            $form->select('category_id', '商品分类')->options(ProductCategory::getSelectOptions())->default('1');
            $form->image('image', '商品主图')->move('upload/images/product_photo')->required()->help('请上传750*750的图片');
            $form->text('stock', '库存')->rules('required|integer');
            $form->text('sales_volume', '销量')->rules('required|integer');
            $form->switch('status', '是否上架')->states([
                'on' => ['value' => 1, 'text' => '上架', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '下架', 'color' => 'default'],
            ]);

        })->tab('商品属性', function ($form) {
            $form->hasMany('product_attr', '添加规格', function (Form\NestedForm $form) {
                $form->text('name', '名称')->required();
                $form->text('code', '编号')->required();
                $form->select('category_id', '分类')->options(ProductCategory::pluck('name','id'))->load('product_attribute', '/admin/product_attribute');
                $form->select('product_attribute','属性')->options(function($id){
                    return ProductAttribute::where('id',$id)->pluck('name','id');        //回显
                })->load('product_attribute_select', '/admin/product_attribute_select');
                $form->select('product_attribute_select','属性值')->options(function($id){
                    return ProductAttributeSelect::where('id',$id)->pluck('value','id');        //回显
                });
                $form->text('stock', '库存')->required();
                $form->text('sales_volume', '销量')->required();
                $form->image('image', '图片')->move('upload/images/product_photo')->required()->help('请上传750*750的图片');
                $form->currency('price', '价格')->required();
            });
        })->tab('商品规格', function ($form) {
            $form->hasMany('product_sku_specs_value', '添加规格', function (Form\NestedForm $form) {
                $form->select('group_id','规格组')->options(

                    ProductSpecsGroup::pluck('name','id')          //回显

                )->load('product_specs', '/admin/product_specs');
                $form->select('product_specs','规格')->options(function($id){

                    return ProductSpecs::where('id',$id)->pluck('name','id');        //回显

                })->load('specs_select_id', '/admin/product_specs_select');
                $form->select('specs_select_id','规格值')->options(function($id){
                    return ProductSpecsSelect::where('id',$id)->pluck('value','id');        //回显

                });
            });
        })->tab('商品组图', function ($form) {
            $form->multipleFile('product_album', '商品相册')->removable()->uniqueName()->move('upload/images/product_photo')->attribute([])->help('请上传750*750的图片');
        })->tab('商品详情', function ($form) {
            $form->UEditor('details', '商品详情')->required();
        });

        //表单保存后回调
        $form->saved(function (Form $form) {
            //获取保存后的id
            $form->model()->id;
            $key = 'product_attribute_select';
            $arr = assoc_unique($form->product_attr, $key);
            $newArr1 = [];
            $newArr2 = [];
            if(!empty($arr)){
                foreach ($arr as $key=>$item){
                    //属性分组
                    $newArr1[$item['product_attribute']][] = $item;
//                    foreach ($arr as $k=>$v){
//                        if($v['product_attribute_select'] != $item['product_attribute_select']){
//                            //组成sku商品数据
//                            $newArr2[] = [
//                                'data'=>$item,
//                                'attr'=>$item['product_attribute_select'].'_'.$v['product_attribute_select'],
//                            ];
//                        }
//                    }
                }
            }
            dump($newArr1);
            if(!empty($newArr1)){
                foreach ($newArr1 as $key=>$item){
                    //属性分组

                    foreach ($item as $kk=>$vv){
                        $as = [];
                        foreach ($newArr1 as $kkk=>$vvv){

                            foreach ($vvv as $kkkk=>$vvvv){
                                if($vvvv['product_attribute_select'] != $vv['product_attribute_select']){
                                    //组成sku商品数据
                                    $as[] = $vvvv['product_attribute_select'];
                                }
                            }
                        }
                        $newArr2[] = $as;
                    }
                }
            }
            dump($newArr2);die;
            //重新组装商品sku数据
        });

        return $form;
    }
}
