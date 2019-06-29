<?php

namespace app\api\model;

use think\Model;

class Product extends Model
{
   protected $autoWriteTimestamp = 'datetime';
   protected $hidden = [
      'delete_time', 'main_img_id', 'pivot', 'from',
      'create_time', 'update_time'
   ];
   /*
    * 公共方法
     * @param $null
     * @return null | productItem
     * by 朱政
    */
   public function footer()
   {
      return $this->hasMany('ProductImage', 'product_id', 'id');
   }
   /*
    * 公共方法
     * @param $null
     * @return null | productItem
     * by 朱政
    */
   public function items()
   {
      return $this->belongsTo('productItem', 'id', 'id');
   }
   /*
    * 公共方法 img
     * @通过模型的img_id 关联模型image的外键id查找
     * @param $null
     * @return null | imgurl
     * by 朱政
    */
   public function img()
   {
      return $this->belongsTo('Image', 'img_id', 'id');
      //$this和image模型通过 关联模型Image的外键'id',和当前模型$this的主键 'img_id'关联在一起
   }
   /*
    * 公共方法 pro
     * @通过模型的category_id 关联模型product_property的外键product_id查找
     * @param $null
     * @return null | product_propertyInfo
     * by 朱政
    */
   public function pro()
   {
      return $this->hasMany('ProductProperty', 'product_id', 'category_id');
   }
   /*
    * 公共方法 properties
     * @通过模型的category_id 关联模型product_property的外键id查找
     * @param $null
     * @return null | product_propertyInfo
     * by 朱政
    */
   public function properties()
   {
      return $this->hasMany('ProductProperty', 'id', 'category_id');
   }
   /*
    * 公共方法：imgID
     * @通过模型的id 关联模型product_item的外键id查找
     * @param $null
     * @return null | img
     * by ys
    */
   public function imgID()
   {
      return $this->belongsTo('ProductItem', 'id', 'id');
      //$this与productItem关联，在product_image模型中通过id来查找id
   }
   public function imgByProductId()
   {
      return $this->hasMany('Image', 'product_id', 'id');
      //$this与Image关联，在image模型中通过id来查找product
   }

   /* 
       公共方法：products
     * 关联product，多对多关系
     * @return null | product_property product category_id id
     * by 朱政
    */
   public function products()
   {
      return $this->belongsToMany(
         'ProductProperty',
         'product',
         'category_id',
         'id'
      );
   }



   /** 
    * 模型自动转json
    * by 朱政
    */
   public function getSaleAttr($value)
   {
      if (empty($value)) {
         return null;
      }
      return json_decode($value);
   }
   public function getComboAttr($value)
   {
      if (empty($value)) {
         return null;
      }
      return json_decode($value);
   }



   /**
    * 根据id查询当前电池的所有详情信息
    *
    * @param int $id
    * @return json 返回对应电池的数据结构
    */
   public  static  function getDetailById($id)
   {

      $p = self::with('imgByProductId,items.properties')
         ->with([
            'footer' => function ($query) {
               $query->with(['img'])
                  ->order('order', 'asc');
            }
         ])
         ->find($id);
      if ($p['footer']->isEmpty()) {
         $detailUrl = [];
      } else {
         $detailUrl = [];
         foreach ($p['footer'] as $v) {
            array_push($detailUrl, IMGFROM . $v['img']['url']);
         }
         $goodInfo['detailUrl'] =  $detailUrl;
         unset($goodInfo['footer']);
      }

      $f = $p['items']['properties'];
      $a = [];
      if (isset($f)) {
         foreach ($f as $key => $value) {
            array_push($a, $value['detail']);
         }
      }


      $goodInfo = [];
      $goodInfo['hid'] = $p['id'];
      if (count($p['img_by_product_id'])) {
         $image = [];
         foreach ($p['img_by_product_id'] as $value) {
            $image[] = IMGFROM . $value['url'];
         }
         $goodInfo['image'] = $image;
      } else {
         $goodInfo['image'] = '';
      }
      $goodInfo['classify'] = $p['classify'];
      $goodInfo['freight'] = $p['freight'];
      $goodInfo['sale'] = $p['sale'];
      $goodInfo['combo'] = $p['combo'];
      $goodInfo['name'] = $p['name'];
      $goodInfo['price'] = $p['price'];
      $goodInfo['application'] = $a;
      $goodInfo['sales_num'] = $p['sales_num'];
      $goodInfo['detail'] = $p['type'];
      $goodInfo['specifications'] = json_decode($p['specifications']);
      $goodInfo['detailUrl'] =  $detailUrl;
      //$goodInfo['specifications'] = preg_replace("/[\s+]/i", '', $goodInfo['specifications']);
      return $goodInfo;
   }
   /**
    * 静态方法：getAllGood 获取商品的列表
    * 获取商品列表
    * @param null
    * @return null | Product
    * by ys
    */
   public static function getAllGood()
   {


      $allInfo = self::field('id,sales_num, name, hot, price, category_id')->with(['imgID', 'imgID.imgUrl'])
         ->select();

      $allInfoArray = [
         'ER' => [],
         'CR' => [],
         'LP' => [],
         'Hot_Sale' => []
      ];
      foreach ($allInfo as $key => $value) {
         $valueNeed = [];

         $valueNeed['image'] = IMGFROM . $value['img_i_d']['img_url']['url'];
         $valueNeed['name'] = $value['name'];
         $valueNeed['price'] = $value['price'];
         $valueNeed['sales_num'] = $value['sales_num'];
         $valueNeed['hid'] = $value['id'];


         if ($value['category_id'] == 1) {
            array_push($allInfoArray['ER'], $valueNeed);
         } else if ($value['category_id'] == 2) {
            array_push($allInfoArray['CR'], $valueNeed);
         }
         if ($value['hot'] == 1 && $value['category_id'] != 3) {
            array_push($allInfoArray['Hot_Sale'], $valueNeed);
         }
      }
      return $allInfoArray;
   }
   public static function getInfoByProductId($id)
   {
      $info = self::with('imgByProductId')->where('id', '=', $id)->find();
      $returnInfo = [];
      $returnInfo['price'] = $info->price;
      $returnInfo['specifications'] = $info->specifications;
      $returnInfo['img'] = $info->img_by_product_id;
      $returnInfo['img'] = IMGFROM . $returnInfo['img'][0]['url'];
      // $returnInfo = json_decode($returnInfo);

      return $returnInfo;
   }
}
