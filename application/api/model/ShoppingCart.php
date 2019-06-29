<?php
namespace app\api\model;

use think\Db;
use think\Exception;

class ShoppingCart extends BaseModel
{

    //关联查询方法
    public function productinfo()
    {
        return $this->hasOne('Product', 'id','product_id')->field('name , price, img_id,id,freight ,classify,sale');
    }
    public function productImg()
    {
        return $this->hasOne('Image', 'product_id','product_id');
    }

    //静态查询方法
    
    public static function checkOneGood($where){

        $good = self::where($where)->find();

        return $good;
    }
    public static function getCount($where){

        $count = self::where($where)->count();

        return $count;
    }

    public static function checkAllGood($where){

        $good = self::where($where)->select();

        return $good;
    }

    public static function checkAllGoodInfo($uid){

        $good = self::field('product_id,count,charger_count')->where('user_id','=',$uid )->with('productinfo,productImg')->select();

        return $good;
    }

    public static function addOneGood($data){

        $insertData = Db::name('shopping_cart')->insert($data); 

        return $insertData;
    }

    public static function updateGood($id, $data){

        $insertData = Db::name('shopping_cart')->where('id', $id)->data($data)->update();

        return $insertData;
    }
    public static function deleteOne($uid,$pid){

        $where = [
            ['user_id', '=', $uid],
            ['product_id', '=', $pid ]
        ];
        $result = self::where($where)->delete();

        return $result;
    }
}