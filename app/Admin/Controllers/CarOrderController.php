<?php

namespace App\Admin\Controllers;

use App\CarOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use App\CarUser;
use Encore\Admin\Widgets\Table;

class CarOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CarOrder());
        $user_obj = Auth::guard('admin')->user();

        $grid->column('id', __('ID'));

        $grid->column('car_user_id', __('车主'))->display(function ($car_user_id){
            $user = CarUser::all()->find($car_user_id);
            return $user->username.'--'.$user->licence_number;
        });;

        $grid->column('total_price', __('总价/实收'));

        $grid->column('information', __('订单明细'))
            ->display(function (){
            return '点击展开详情';
        })->expand(function ($model) {

//            var_dump($model->information,$model->id,is_array($model->information));
            $information = (array)$model->information;
            return new Table(['备注/说明', '定价', '数量', '项目', '折扣', '小计', '售价'], $information);
        });

        $grid->column('created_at', __('创建时间'));

        $grid->column('user_id', __('订单操作员'));

        $grid->column('status', __('状态'))->using([
            0 => '已取消',
            1 => '进行中',
            2 => '已完成',
        ], '未知')->dot([
            0 =>'info',
            1 => 'primary',
            2 => 'success',
        ], 'warning')->filter([
            0 => '已取消',
            1 => '进行中',
            2 => '已完成',
        ]);

        if ($user_obj->id == 1) {
            // code...

        } else {
            $grid->model()->whereIn('user_id', [$user_obj->id]);
        }

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
        });

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(1/2, function ($filter) {
                $filter->equal('car_user_id','用户信息')->select(function(){
                    $users = CarUser::all();
                    $user_arr = [];
                    if (empty($users)){

                    }else{

                        foreach ($users as $key=>$user){
                            $user_arr[$user->id] = $user->username.'--'.$user->licence_number;
                        }
                    }

                    return $user_arr;
                });
            });
            $filter->column(1/2, function ($filter) {
                $filter->like("information", '订单明细');
            });


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
        $show = new Show(CarOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('status', __('Status'));
        $show->field('total_price', __('Total price'));
        $show->field('car_user_id', __('Car user id'));
        $show->field('user_id', __('User id'));
        $show->field('information', __('Information'));
        $show->field('updated_at', __('Updated at'));
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CarOrder());
        $user_obj = Auth::guard('admin')->user();

        $form->select('status', __('设置状态'))->options([0 => '已取消',1 => '进行中', 2 => '已完成'])->width('50%');


        $form->select('car_user_id', __('车主关联'))->options(function () {
            //将车主列表全部查出来
            $users = CarUser::all()->all();
            $res_data = [];
            if (empty($users)) {
                return $res_data;
            }else{
                foreach ($users as $key=>$user){
                    $res_data[$user->id] = '姓名：'.$user->username.'--手机号：'.$user->phone.'--车牌号：'.$user->licence_number;
                }
            }
            return $res_data;
        });

        $form->text('user_id', __('当前操作员'))->default($user_obj->id)->help('(不可更改)用户名：'.$user_obj->name)->readonly()->width('20%');

        $form->table('information','订单明细', function ($table) {

//            $table->text('type','类别');
            $table->text('project','项目');
            $table->text('desc','备注/说明');
//            $table->text('price_type','价别');
            $table->currency('price','定价')->symbol('￥');
            $table->rate('discount','折扣');
            $table->currency('sale_price','售价')->symbol('￥');
            $table->number('number','数量');
            $table->currency('subtotal','小计')->symbol('￥');


        });

        $form->currency('total_price', __('订单总价'))->symbol('￥');

        return $form;
    }
}
