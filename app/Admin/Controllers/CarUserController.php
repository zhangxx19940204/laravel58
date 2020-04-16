<?php

namespace App\Admin\Controllers;

use App\CarUser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CarUserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CarUser());

        $user_obj = Auth::guard('admin')->user();

        $grid->column('id', __('ID'));
        $grid->column('username', __('用户名'));
        $grid->column('phone', __('手机号'));
        $grid->column('email', __('邮箱'));
        $grid->column('frame', __('车架'));
        $grid->column('engine_number', __('发动机号'));
        $grid->column('vin_number', __('VIN码'));
        $grid->column('brand', __('品牌'));
        $grid->column('models', __('车型'));
        $grid->column('mileage', __('里程数'));
        $grid->column('licence_number', __('车牌号'));
        $grid->column('user_id', __('当前用户ID'));
        $grid->column('extra_info', __('其他信息'))->display(function ($extra_info) {
            $str = '';
            foreach ($extra_info as $key=>$single_info){
                $str .=  '标题：('.$single_info['key'].')-内容：('.$single_info['value'].')-备注/说明：('.$single_info['desc'].")<br/>";
            }
            return $str;
        });;

        if ($user_obj->id == 1) {
            // code...

        } else {
            $grid->model()->whereIn('user_id', [$user_obj->id]);
        }

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉查看
            $actions->disableView();
        });

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(1/2, function ($filter) {
                $filter->like('username', '用户名');
                $filter->like('phone', '手机号');
                $filter->like('licence_number', '车牌号');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like("extra_info", '其他信息');
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
        $show = new Show(CarUser::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('username', __('Username'));
        $show->field('phone', __('Phone'));
        $show->field('email', __('Email'));
        $show->field('frame', __('Frame'));
        $show->field('engine_number', __('Engine number'));
        $show->field('vin_number', __('Vin number'));
        $show->field('brand', __('Brand'));
        $show->field('models', __('Models'));
        $show->field('mileage', __('Mileage'));
        $show->field('licence_number', __('Licence number'));
        $show->field('user_id', __('User id'));
        $show->field('test', __('Test'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CarUser());
        $user_obj = Auth::guard('admin')->user();
        $form->text('username', __('名字'));
        $form->mobile('phone', __('手机号'));
        $form->email('email', __('邮箱'));
        $form->text('frame', __('车架'));
        $form->text('engine_number', __('发动机号'));
        $form->text('vin_number', __('VIN码'));
        $form->text('brand', __('品牌'));
        $form->text('models', __('车型'));
        $form->number('mileage', __('里程数'));
        $form->text('licence_number', __('车牌号'));

        $form->table('extra_info','其他信息', function ($table) {
            $table->text('key','标题');
            $table->text('value','内容');
            $table->text('desc','备注/说明');
        });


        $form->text('user_id', __('管理员id'))->default($user_obj->id)->help('(不可更改)用户名：'.$user_obj->name)->readonly()->width('20%');
        return $form;
    }

}
