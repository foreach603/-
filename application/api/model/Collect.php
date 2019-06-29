<?php
namespace app\api\model;

use think\Db;
use Think\Model;

class Collect extends Model
{
   protected $autoWriteTimestamp = true;
   public function img()
   {
      return $this->hasMany('Image', 'id', 'product_id');
   }
   public function price()
   {
      return $this->hasOne('Product', 'id', 'product_id')->field('price , id,img_id,specifications,name');
   }

   public static function delById($id)
   {
      $result = self::where('id', $id)->delete();
      return $result;
   }
   public static function getCount($uid)
   {
      $where = [
         ['user_id', '=', $uid]
      ];
      $result = self::where($where)->count();
      return $result;
   }
   public static function getAll($uid)
   {
      $result = self::where('user_id', '=', $uid)->with(['price', 'price.img'])->select();

      return $result;
   }
   public static function getAllProduct($uid)
   {
      $result = self::field('product_id')->where('user_id', '=', $uid)->select();

      return $result;
   }
   public static function is_exist($uid, $pid)
   {
      $map = [
         ['user_id', '=', $uid],
         ['product_id', '=', $pid]
      ];
      $result = Db::name('collect')->where($map)->count();
      return $result;
   }
   public static function delWithPid($uid, $product_id)
   {
      $result = self::where('user_id', $uid)
         ->where('product_id', $product_id)
         ->delete();
      return $result;
   }
}
