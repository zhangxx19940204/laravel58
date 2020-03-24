<?php

namespace App\Http\Controllers\miniapp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Log;
use Illuminate\Support\Facades\Input;

class WeChatController extends Controller
{
    //
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve(Request $request)
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.mini_program.default');
        
        $media_page = Input::get('media_page', '');
        if($media_page == ''){
        	return '请重新，去生成';
        }
        
        $media_page_extra = $media_page;
        //文件没有问题，现在去生成文件的小程序码  例如 ： 腩潮鲜/行业现状任.mp4
    	$media_arr = explode('/',$media_page);
    	$media_file = $media_arr[count($media_arr)-1];
    	$media_name = explode('.',$media_file)[0];  //文件的名字
    	
    	//获得文件名字后，进行检测是否已存在小程序码
    	$qrcode_dir = "storage/qrcode/";

		// 以升序排序 - 默认
		$qrcode_arr = scandir($qrcode_dir);
		$qrcode_name = $media_name.'.png';
		
		// var_dump($qrcode_arr,$media_name,$media_file,$qrcode_name);
		// var_dump($media_page);
		
		if(in_array($qrcode_name,$qrcode_arr)){
			//查找到相应的文件直接展示
			return '<img src="https://www.wintersweet.cn/storage/qrcode/'.$media_name.'.png"  alt="" />';
		}else{
			//未查找到去生成
			
			$response = $app->app_code->get('pages/index/index?media_page='.trim($media_page), []);

			if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			    $filename = $response->saveAs('storage/qrcode/', $media_name.'.png');
			}
			//文件已生成，已保存，下面进行展示
			return '<img src="https://www.wintersweet.cn/storage/qrcode/'.$media_name.'.png"  alt="" />';
		}

    	
        
        return ;
		
		
		// return ;
        // $app = app('wechat.official_account');
        // $app->server->push(function($message){
        //     return "欢迎关注 overtrue！";
        // });

        // return $app->server->serve();
    }
    
    public function get_banner(Request $request){
    	$data = $request->all();
    	$banner_arr = [];
    	if(!array_key_exists('media_name', $data)){
    		$data['media_name'] = '';
    	}
    	if(strpos($data['media_name'],'腩潮鲜') !==false){
			//包含腩潮鲜
			$banner_arr = ['http://static.wintersweet.cn//miniapp/banner/ncx/ncx1.png','http://static.wintersweet.cn//miniapp/banner/ncx/ncx2.png','http://static.wintersweet.cn//miniapp/banner/ncx/ncx3.png'];
			$company_phone = '400-016-6667';
			
		}elseif (strpos($data['media_name'],'原时烤肉') !==false) {
			//包含原时
			$banner_arr = ['http://static.wintersweet.cn//miniapp/banner/yuanshi/ys1.png','http://static.wintersweet.cn//miniapp/banner/yuanshi/ys2.png','http://static.wintersweet.cn//miniapp/banner/yuanshi/ys3.png'];
			$company_phone = '400-066-0027';
			
		}else{
			//不包含 腩潮鲜 和 原时
			$banner_arr = ['http://static.wintersweet.cn//miniapp/banner/ds/ds1.png'];
			$company_phone = '400-016-6667';
		}
		$res_arr = ['banner_list'=>$banner_arr,'company_phone'=>$company_phone];
		return response()->json($res_arr);

    }
    
    
    //微信小程序的点餐系列接口
    
    /**
    * 获取用户openid
    */
    public function getUserOpenId(){
    	Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.mini_program.test');
        
        $code = Input::get('code', '');
        
        if(empty($code)){
        	return response()->json(['code'=>-2,'msg'=>'参数不完整']);	
        }else{
        	$openid_arr = $app->auth->session($code);
        	return response()->json(["code"=>0,"msg"=>$openid_arr]);
        }
        
    }    
    
    /**
    * 点餐用户注册到后台
    */
    public function register(Request $request){
    	Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.mini_program.test');
        $data = $request->all(); //前端传来的用户数组
        
        $user = DB::table('weixin_user')->where('openId', '=', $data['openId'])->get()->toarray();
        if(!empty($user)){
        	return response()->json(["code"=>-2,"msg"=>"该用户已注册"]);		
        }else{
        	//开始执行插入操作
        	DB::table('weixin_user')->insert(
			    ['nickName' =>$data['nickName'],'avatarUrl' =>$data['avatarUrl'],'province' =>$data['province'],'gender' =>$data['gender'],'city' =>$data['city'],'openId' =>$data['openId']]
			);

        	return response()->json(["code"=>0,"msg"=>"注册成功"]);
        }
        
        
    }
    
    /**
    * 登录 
    */
    public function login(){
    	Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.mini_program.test');
        
        $openId = Input::get('openId', '');
        
        if(empty($openId)){
        	return response()->json(['code'=>-2,'msg'=>'参数不完整']);
        }else{
        	//根据openId 去更新用户的登录时间
        	DB::table('weixin_user')->where('openId', $openId)->update(['last_loginTime' =>date('Y-m-d H:i:s')]);
        	
        	return response()->json(["code"=>0,"msg"=>'登录成功']);
        }
        
    }     
    
    /**
    * 获取商品列表
    */
    public function getfoodList(){
    	Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $app = app('wechat.mini_program.test');
        
        $menu_list = DB::table('weixin_menu')->get();
        $food_list = [];
        foreach ($menu_list as $key=>$menu){
        	$temp=[];
        	$temp['name'] = $menu->type;
        	$temp['foods'] = DB::table('weixin_foods')
        	->where('status', '=', 1)
        	->where('type', '=', $menu->id)
        	->get()->toarray();
        	$food_list[] = $temp;
        }
        
        if(empty($food_list)){
        	return response()->json(['code'=>0,'data'=>[]]);
        }else{
        	
        	return response()->json(["code"=>0,"data"=>$food_list]);
        }
        
    }    
    
}
