<?php

namespace App\Admin\Controllers;

use App\EmailData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class EmailDataController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '邮件数据';
    

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EmailData);
        $user_obj = Auth::guard('admin')->user();
        
        $grid->header(function ($query) {
        	$user_obj = Auth::guard('admin')->user();
        	
		    return $user_obj->id;
		});
        

        $grid->column('id', __('Id'));
        $grid->column('res_name', __('Res name'));
        $grid->column('res_phone', __('Res phone'));
        $grid->column('res_date', __('Res date'));
        $grid->column('res_from', __('Res from'));
        $grid->column('platform', __('Platform'));
        $grid->column('complete_info', __('Complete info'));
        $grid->column('econfig_id', __('Econfig id'));
		//下面进行测试邮件的东西
		
		// $email_data = new EmailData;
  //      $res_mail_folder_list = $email_data->get_mail_list($single_data);
        
        
        
		if ($user_obj->id == 1) {
			// code...
		} else {
			$grid->model()->whereIn('user_id', [$user_obj->id]);
		}
		
		var_dump(123456);
		
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
        $show = new Show(EmailData::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('res_name', __('Res name'));
        $show->field('res_phone', __('Res phone'));
        $show->field('res_date', __('Res date'));
        $show->field('res_from', __('Res from'));
        $show->field('platform', __('Platform'));
        $show->field('complete_info', __('Complete info'));
        $show->field('econfig_id', __('Econfig id'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EmailData);

        $form->text('res_name', __('Res name'));
        $form->text('res_phone', __('Res phone'));
        $form->text('res_date', __('Res date'));
        $form->text('res_from', __('Res from'));
        $form->text('platform', __('Platform'));
        $form->textarea('complete_info', __('Complete info'));
        $form->number('econfig_id', __('Econfig id'));

        return $form;
    }
    
    public function get_mail_data(){
    	return '123456';
    }
}
