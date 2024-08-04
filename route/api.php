<?php

/**
 * api 接口的路由
 */

// 获取验证码
use app\middleware\ApiUserAuth;
use think\facade\Route;

// 路由分组来控制接口版本
// 无需登录
Route::group('api/v1/', function () {
    Route::post('user/send/code', 'api.v1.User/sendCode');
    Route::post('user/phone/login', 'api.v1.User/phoneLogin');

    // 指定分类下的话题列表 注意顺序 都是 category/ 前缀起步
    Route::get('category/:category_id/topic/:page', 'api.v1.Topic/index');
    // 分类列表
    Route::get('category/:type', 'api.v1.Category/index');
    // 话题详情
    Route::get('topic/:id', 'api.v1.Topic/read');
});


// 登录之后才能操作
Route::group('api/v1/', function () {
    Route::post('user/send/code2', 'api.v1.User/sendCode2');
    // 修改密码
    Route::post('user/change/password', 'api.v1.User/changePassword');
    // 用户密码登录
    Route::post('user/login', 'api.v1.User/login');
    // 退出登录
    Route::post('user/logout', 'api.v1.User/logout');
    // 上传图片
    Route::post('upload', 'api.v1.Image/upload');
    // 发布帖子
    Route::post('article/save', 'api.v1.Article/save');
})->middleware([
    ApiUserAuth::class
]);