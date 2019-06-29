<?php
namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use think\Controller;
use app\api\service\Order as OrderService;
use app\api\validate\PagingParameter;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\OrderException;
use app\api\model\UserAddress;
use app\api\model\Express;

class Order extends Controller
{
   // 用户在选择商品后 向API提交包含选择商品的相关信息
   // API在接收到信息后 需检查订单相关商品的库存量
   // 有库存 把订单数据存入数据库 下单z成功 返回客户端消息 告诉客户端可以支付了
   // 调用支付接口 进行支付
   // 扣款时 还需要检测库存量
   // 服务器就可以调用 wx支付结口 进行支付
   // 小程序根据服务器返回的结果发起微信支付
   // 微信会返回支付的结果 (异步调用)
   // 即使成功 也需要在进行一次库存量的检测
   // 支付成功 扣除库存量
   // 支付失败 返回支付失败的结果



   public function getSummaryByUser($page = 1, $size = 5)
   {
      (new PagingParameter())->goCheck();
      $uid = TokenService::getCurrentUid();
      $paginateOrders = OrderModel::getSummaryByUser($uid, $page, $size);
      if ($paginateOrders->isEmpty()) {
         return [
            'data' => [],
            'current_page' => $paginateOrders->getCurrentPage()
         ];
      }
      $data = $paginateOrders->hidden(['snap_items', 'snap_address', 'prepay_id'])
         ->toArray();
      return [
         'data' => $data,
         'current_page' => $paginateOrders->getCurrentPage()
      ];
   }

   public function getDetail($id)
   {
      (new IDMustBePositiveInt())->goCheck();
      $orderDetail = OrderModel::get($id);
      if (!$orderDetail) {
         throw new OrderException();
      }
      return $orderDetail->hidden(['prepay_id']);
   }
   public function placeOrder()
   {
      $products = input('post.products/a');  //必须加/a才能获取数组

      $num = count($products) - 1;
      $remark = $products[$num]['remark'];
      $address_id = $products[$num]['address_id'];
      $address = UserAddress::get($address_id);
      unset($products[$num]);

      //return $products;
      $uid  = TokenService::getCurrentUid();
      $order = new OrderService();
      $status = $order->place($uid, $products, $remark, $address);
      return $status;
   }
   public function exitOrder($id)
   {
      (new IDMustBePositiveInt())->goCheck();
      $is_delete = 0;
      $res = OrderModel::where('id', '=', $id)
         ->update(['is_delete' => $is_delete]);
      if ($res) {
         return '201';
      }
   }
   public function DeleteOrder($id)
   {
      (new IDMustBePositiveInt())->goCheck();
      $is_delete = 3;
      $res = OrderModel::where('id', '=', $id)
         ->update(['is_delete' => $is_delete]);
      if ($res) {
         return '201';
      }
   }
   public function recoverOrder($id)
   {
      (new IDMustBePositiveInt())->goCheck();
      $is_delete = 1;
      $res = OrderModel::where('id', '=', $id)
         ->update(['is_delete' => $is_delete]);
      if ($res) {
         return '201';
      }
   }
   public function getExpress($id)
   {
      (new IDMustBePositiveInt())->goCheck();
      $info = Express::getExpress($id);
      if($info == null){
         return 500;
      }
      return $info;
   }
}
