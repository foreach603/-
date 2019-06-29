<?php
namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;
use app\lib\enum\OrderStatusEnum;

class Order
{
   // 订单的商品列表 也就是客户端传来的products
   protected $oProducts;
   // 真实的商品信息 包括库存
   protected $products;
   protected $uid;

   public function place($uid, $oProducts, $remark, $address)
   {
      // oProducts 与 products对比
      // products从数据库查询出来
      $this->oProducts = $oProducts;
      //return $this->oProducts;
      $this->products  = $this->getProductByOrder($oProducts);
      $this->uid       = $uid;

      $status          = $this->getOrderStatus();



      if (!$status['pass']) {
         $status['order_id'] = -1;
         return $status;
      }

      //开始创建订单
      $snap     = $this->snapOrder($status);
      $snap['remark'] = $remark;
      $snap['address'] = $address;
      $order         = $this->createOrder($snap);
      $order['pass'] = true;
      return $order;
   }

   public function checkOrderStock($orderID)
   {
      $oProducts = OrderProduct::where('order_id', $orderID)
         ->select();
      $this->oProducts = $oProducts;
      $this->products = $this->getProductByOrder($oProducts);
      $status = $this->getOrderStatus();
      // 根据orderID 对应在order表查询订单内价格
      $oProductsNew = OrderModel::get($orderID);
      // $truePrice = 0;
      // foreach ($oProductsNew['snap_items'] as  $op) {
      //    $truePrice += $op->totalPrice;
      // }
      $truePrice = $oProductsNew['total_price'];
      //  价格二次对比 如果不同则覆盖
      if ($truePrice > $status['orderPrice']) {
         $status['orderPrice'] = $truePrice;
      }
      return $status;
   }

   private function createOrder($snap)
   {
      // return $snap;
      Db::startTrans();
      try {
         $orderNo             = $this->makeOrderNo();
         $order               = new OrderModel();
         $order->user_id      = $this->uid;
         $order->order_no     = $orderNo;
         $order->total_price  = $snap['orderPrice'];
         $order->total_count  = $snap['totalCount'];
         $order->snap_img     = $snap['snapImg'];
         $order->snap_name    = $snap['snapName'];
         $order->snap_address = $snap['snapAddress'];
         $order->remark = $snap['remark'];
         $order->address = $snap['address'];
         $order->snap_items   = json_encode($snap['pStatus']);
         $order->save();


         $orderID     = $order->id;
         $create_time = $order->create_time;
         foreach ($this->oProducts as &$p) {
            $p['order_id'] = $orderID;
         }
         $orderProduct = new OrderProduct();
         $orderProduct->saveAll($this->oProducts);
         Db::commit();
         return [
            'order_no'    => $orderNo,
            'order_id'    => $orderID,
            'create_time' => $create_time,
         ];
      } catch (Exception $ex) {
         Db::rollback();
         throw $ex;
      }
   }

   public static function makeOrderNo()
   {
      $yCode   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
      $orderSn =
         $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
            'd'
         ) . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
            '%02d',
            rand(0, 99)
         );
      return $orderSn;
   }

   private function snapOrder($status)
   {
      $snap = [
         'orderPrice'  => 0,
         'totalCount'  => 0,
         'pStatus'     => [],
         'snapAddress' => null,
         'snapName'    => '',
         'snapImg'     => '',
      ];

      $snap['orderPrice']  = $status['orderPrice'];
      $snap['totalCount']  = $status['totalCount'];
      $snap['pStatus']     = $status['pStatusArray'];
      $snap['snapAddress'] = json_encode($this->getUserAddress());
      $snap['snapName']    = $this->products[0]['name'];
      $snap['snapImg']     = $this->products[0]['img_id'];

      if (count($this->products) > 1) {
         $snap['snapName'] .= '等';
      }
      return $snap;
   }

   private function getUserAddress()
   {
      $userAddress = UserAddress::where('user_id', '=', $this->uid)
         ->find();
      if (!$userAddress) {
         throw new UserException([
            'msg'       => '用户收货地址不存在 , 下单失败',
            'errorCode' => 60001,
         ]);
      }
      return $userAddress->toArray();
   }

   private function getOrderStatus()
   {
      $status = [
         'pass'         => true,
         'orderPrice'   => 0,
         'totalCount'   => 0,
         'pStatusArray' => [],
      ];
      foreach ($this->oProducts as $oProduct) {

         $charger = array_key_exists('charger_num', $oProduct);
         if ($charger) {
            $pStatus = $this->getProductStatus(
               $oProduct['product_id'],
               $oProduct['count'],
               $oProduct['charger_num'],
               $this->products
            );
            if (!$pStatus['haveStock']) {
               $status['pass'] = false;
            }
            $status['totalCount'] += $pStatus['count'];
            $status['orderPrice'] += $pStatus['totalPrice'];
            $pStatus['charger_num'] = $oProduct['charger_num'];
            array_push($status['pStatusArray'], $pStatus);
         } else {
            $pStatus = $this->getProductStatus(
               $oProduct['product_id'],
               $oProduct['count'],
               $oProduct['charger_num'] = "",
               $this->products
            );
            if (!$pStatus['haveStock']) {
               $status['pass'] = false;
            }

            $status['totalCount'] += $pStatus['count'];
            $status['orderPrice'] += $pStatus['totalPrice'];
            array_push($status['pStatusArray'], $pStatus);
         }
      }
      $pinkage = $this->checkPinkage($this->oProducts);
      $status['orderPrice'] += $pinkage;
      return $status;
   }

   private function checkPinkage($products)
   {
      $fiveStr = 0;
      $tenStr = 0;
      foreach ($products as $p) {
         $nowOne = Product::get($p['product_id']);
         // 如果订单内包含电动车电池 直接包邮
         if ($nowOne->category_id == 4) {
            return 0;
         }
         // 如果包邮数在5个及以上  统计此类电池数量总和
         if ($nowOne['sale']->packages_num == 5) {
            $fiveStr += $p['count'];
         }
         // 如果包邮数在10个及以上 统计总和
         if ($nowOne['sale']->packages_num == 10) {
            $tenStr += $p['count'];
         }
      }
      //  循环过后 判断 是否满足不同区间包邮前提
      // 满足则包邮 反之返回14邮费
      if ($fiveStr >= 5 || $tenStr >= 10) {
         return 0;
      } else {
         return OrderStatusEnum::PACKAGE;
      }
   }

   private function getProductStatus($oPID, $oCount, $chargerNum, $products)
   {
      $pIndex  = -1;
      $pStatus = [
         'id'         => null,
         'haveStock'  => false,
         'count'      => 0,
         'name'       => '',
         'totalPrice' => 0,
      ];

      for ($i = 0; $i < count($products); $i++) {
         if ($oPID == $products[$i]['id']) {
            $pIndex = $i;
         }
      }

      if ($pIndex == -1) {
         throw new OrderException([
            'msg' => 'id为' . $oPID . '的商品不存在, 订单创建失败',
         ]);
      } else {
         $product               = $products[$pIndex];
         $pStatus['id']         = $product['id'];
         $pStatus['count']      = $oCount;
         $pStatus['name']       = $product['name'];
         $pStatus['totalPrice'] = $this->checkPriceByProduct($product, $oCount, $chargerNum);

         if ($product['stock'] - $oCount >= 0) {
            $pStatus['haveStock'] = true;
         }
      }
      return $pStatus;
   }

   private function checkPriceByProduct($product, $oCount, $chargerNum)
   {
      $thisOne = Product::get($product['id']);
      //  进行判断 电动车是否包含充电器 计算充电器价格并相加
      if ($thisOne->category_id == 4) {
         if ($chargerNum !== "") {
            $chargerPrice = $thisOne['sale']->charger * $chargerNum;
            $batteryPrice = $thisOne->price * $oCount;
            $price = $chargerPrice + $batteryPrice;
         } else {
            $price = $thisOne->price * $oCount;
         }
      } elseif ($thisOne->category_id == 1) {  //ER电池 

         $price = $thisOne->price * $oCount;
      } elseif ($thisOne->category_id == 2) { //CR电池

         $discount = $thisOne['sale']->isDiscount;
         $discount_num = $thisOne['sale']->discount_num;
         $favorable_price = $thisOne['sale']->favorable_price;

         if ($discount == 1) {  // 判断是否有优惠套餐
            // 如果数量满足优惠  按优惠价处理
            $price = $oCount >= $discount_num ? $favorable_price * $oCount : $thisOne->price * $oCount;
         } else {
            $price = $thisOne->price * $oCount;
         }
      }
      return $price;
   }

   //根据订单查找真实的商品信息
   private function getProductByOrder($oProducts)
   {
      $oPIDs = [];
      foreach ($oProducts as $item) {
         array_push($oPIDs, $item['product_id']);
      }
      $products = Product::all($oPIDs)
         ->visible(['id', 'price', 'name', 'stock', 'img_id'])
         ->toArray();

      return $products;
   }
   
}
