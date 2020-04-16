<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('users', UserController::class);
    
    $router->resource('email-configs', EmailConfigController::class);
    
    $router->resource('email-datas', EmailDataController::class);

    $router->resource('email-passes', EmailPassController::class);

    //car管理系统
    $router->resource('car-users', CarUserController::class);
    $router->resource('car-orders', CarOrderController::class);

});

