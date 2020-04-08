<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//微信小程序 

//视频展示系列接口
Route::any('/wechat', 'miniapp\WeChatController@serve');
Route::any('/get_banner', 'miniapp\WeChatController@get_banner');

// 点餐系列接口
Route::any('/getUserOpenId', 'miniapp\WeChatController@getUserOpenId');
Route::any('/register', 'miniapp\WeChatController@register');
Route::any('/login', 'miniapp\WeChatController@login');
Route::any('/getfoodList', 'miniapp\WeChatController@getfoodList');


Route::any('/get_mail_list', 'extra\ExtraController@get_mail_list');

//生成头像的代码

Route::any('/showupload', 'extra\ExtraController@showupload');


Route::any('/uploadimage', 'extra\ExtraController@uploadimage');


//制作首页代码
Route::get('/test', 'loadpage\LoadpageController@test');
