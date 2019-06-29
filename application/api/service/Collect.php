<?php
/* by ys */
namespace app\api\service;
use app\api\model\Collect as CollectModel;
use think\Exception;

class Collect{

     public static function getAll($uid){

        $result = CollectModel::getAll($uid);

        $collects = [];
        foreach( $result as $key => $value ){
            $collects[$key]['hid'] = $value['product_id'];
            $collects[$key]['price'] = $value['price']['price'];
            $collects[$key]['detail'] = $value['price']['specifications'];
            $collects[$key]['model'] = $value['price']['name'];
            $collects[$key]['image'] = IMGFROM.$value['img'][0]['url'];
        }
        return $collects;
    }
    public static function saveOne($array)
    {
        $uid = $array['user_id'];
        $product_id =  $array['product_id'];

        $result = CollectModel::is_exist($uid,$product_id);
        
        return $result;
        
    }
}