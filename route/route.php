<?php
use think\facade\Route;

// 重定向到登录页
Route::get('/', function () {
    return redirect('admin/index/login');
});

// 普通管理员组
Route::group('admin', function () {
    // 路由跳转到登录页
    Route::rule('/', 'admin/home/index', 'get');
    // 路由跳转到注册页
    Route::rule('/logup', 'admin/index/register', 'get');
    // 路由跳转到首页
    Route::rule('/home', 'admin/home/index', 'get');
    // 路由跳转到图片码页
    Route::rule('/code', 'admin/index/genCaptcha', 'get');
    // 路由跳转到个人中心页
    Route::rule('/profile', 'admin/user/index', 'get');
    Route::rule('/update', 'admin/user/update', 'post');
});

return [

];