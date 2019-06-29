<?php
namespace  app\api\model;

use think\Db;
use think\Model;

class UserAddress extends Model
{
   protected $hidden = ['delete_time', 'user_id'];
   protected $autoWriteTimestamp = true;


   public static function deleteById($id)
   {
      $result = self::where('id', '=', $id)
         ->delete();
      return $result;
   }
   public static function saveNewAdd($add)
   {

      $insertData = Db::name('user_address')->insert($add);
      //$result = self->save($add);
      return $insertData;
   }

   public static function showById($id)
   {
      $result = self::where('id', '=', $id)
         ->find();
      return $result;
   }

   public static function doEditById($id, $dataArray)
   {
      $result = self::where('id', '=', $id)
         ->update($dataArray);
      return $result;
   }

   public static function findDefaultById($uid)
   {
      $result = self::where('user_id', '=', $uid)
         ->where('default', 1)
         ->find();
      return $result;
   }

   public static function editDefaultById($id, $arr)
   {
      $result = self::where('id', '=', $id)
         ->update($arr);
      return $result;
   }
   public static function getOne($uid)
   {
      $result = self::where('user_id', '=', $uid)
         ->find();
      return $result;
   }
}
