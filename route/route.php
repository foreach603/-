<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;
//测试接口
Route::get('test', 'api/v1.Banner/test');

Route::get('getIndexImg', 'api/v1.IndexImg/getIndexImg');
//获取所有商品信息（除了电动车电池）
Route::get('getAllinfo', 'api/v1.GetGoodInfo/getAllInfo');
//获取电动车电池信息
Route::get('getBicycle', 'api/v1.GetGoodInfo/getBicycle');
//商品详情页,获取一个商品的信息
Route::get('getOne', 'api/v1.Product/getone');
//登录接口
Route::post('getToken/user','api/v1.Token/getToken');

/*购物车相关*/

//添加购物车商品，如果重复点击就是数量加1
Route::post('shoppingcart','api/v1.ShoppingCart/createOrUpdate');
//购物车商品数量减1
Route::post('shoppingcartMinus','api/v1.ShoppingCart/numMinus');
//获取购物车列表
Route::post('getShoppingcart','api/v1.ShoppingCart/getShoppingCartDate');
//修改购物车内商品数量
Route::post('changeShoppingcartNum','api/v1.ShoppingCart/changeNum');
//删除购物车商品
Route::post('shoppingcartcut','api/v1.ShoppingCart/cutOrDelete');
//在购物车页面收藏商品，如果重复点击就是取消收藏
Route::get('delShopCollect', 'api/v1.ShoppingCart/delCollect');
//在个人信息页面,获取收藏商品数量和购物车商品数量
Route::get('getCount', 'api/v1.ShoppingCart/getCount');
//提交订单时候，删除购物车内商品
Route::post('submitShoppingCart', 'api/v1.ShoppingCart/submitShoppingCart');


/*用户地址相关操作*/

// 显示用户地址
Route::get('addressIndex', 'api/v1.Address/index');
//删除用户地址
Route::get('delAddress', 'api/v1.Address/del');
//编辑用户地址（获取本条用户地址）
Route::get('editAddress', 'api/v1.Address/edit');
//设置默认用户地址
Route::get('default', 'api/v1.Address/setDefault');
//提交修改用户地址
Route::post('doEditAddress', 'api/v1.Address/doEdit');
//添加用户地址
Route::post('addAddress', 'api/v1.Address/add');


/*收藏夹相关操作*/
//添加收藏 
Route::post('addCollect', 'api/v1.Collect/createCollect');
//展示所有收藏
Route::get('collectIndex', 'api/v1.Collect/index');
//删除一项收藏 传递收藏id
Route::get('delCollect', 'api/v1.Collect/del');


/*用户信息相关操作 */
//获取用户信息
Route::get('userIndex', 'api/v1.User/index');
//编辑用户信息
Route::post('editUserIndex', 'api/v1.User/edit');

/*订单页面*/

//订单页面获取用户的默认地址,如果没有设置,就获取第一条
Route::get('getOneAddress', 'api/v1.Address/getOne');

// 订单页面路由
Route::post('order', 'api/v1.Order/placeOrder');
Route::get('orderByUser', 'api/v1.Order/getSummaryByUser');
Route::get('orderDetail', 'api/v1.Order/getDetail');
// 支付页面路由
Route::post('pre_order', 'api/v1.Pay/getPreOrder');
Route::post('notify', 'api/v1.Pay/receiveNotify');

//获取待支付订单
Route::get('getOder', 'api/v1.GetOder/getNoPay');
//删除订单
Route::get('exitOrder', 'api/v1.Order/exitOrder');
//恢复订单
Route::get('recoverOrder', 'api/v1.Order/recoverOrder');
//获取快递信息
Route::get('getExpressInfo', 'api/v1.Order/getExpress');
//删除订单
Route::get('deleteOrder', 'api/v1.Order/DeleteOrder');